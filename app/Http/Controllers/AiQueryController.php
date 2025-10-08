<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Services\AiSqlAgent;
use App\Services\ClickHouseClient;
use App\Services\LlmService;
use Illuminate\Support\Facades\Log;

class AiQueryController extends Controller
{
    /** @var AiSqlAgent */
    private $agent;

    /** @var ClickHouseClient */
    private $ch;

    /** @var LlmService */
    private $llm;

    public function __construct(AiSqlAgent $agent, ClickHouseClient $ch, LlmService $llm)
    {
        $this->agent = $agent;
        $this->ch    = $ch;
        $this->llm   = $llm;
    }

    public function stream(Request $req)
    {
        $req->validate(['question'=>'required|string|max:500']);

        // Tutup session awal supaya middleware 'web' tak mengunci request lama
        if (session()->isStarted()) { session()->save(); }
        // Matikan batas eksekusi untuk streaming panjang (opsional: set angka spesifik)
        @set_time_limit(0);
        @ignore_user_abort(true);
        
        $question = $req->input('question');

        $prep = $this->agent->proposeSql($question);

        return new StreamedResponse(function () use ($prep, $question) {
            // Matikan buffering PHP yang suka menahan output
            @ini_set('output_buffering', 'off');
            @ini_set('zlib.output_compression', '0');
            while (ob_get_level() > 0) { @ob_end_flush(); }
            @ob_implicit_flush(true);

            // ===== A) Minta LLM buat JSON {sql, notes} (non-stream, wajib JSON tunggal) =====
            $prompt = $prep['prompt']
            . "\n\nQuestion: {$prep['question']}\n"
            . "Return exactly ONE valid JSON object. No prose. No code fences.\n"
            . "JSON schema:\n"
            . "{\"sql\": string, \"notes\": string|null}\n"
            . "Rules:\n"
            . "1) Output ONLY the JSON object.\n"
            . "2) No trailing commas. Escape quotes in strings.\n"
            . "3) Keep it under 350 tokens.\n"
            . "4) If unsure, set \"notes\": null and still return JSON.\n";
            $options = [
            'temperature'      => 0,
            'max_tokens'       => 400,   // kecil saja agar tak ngalor-ngidul
            'response_format'  => ['type'=>'json_object'], // jika didukung
            'stop'             => ["\n\n\n"], // opsional, untuk memotong ocehan tambahan
            ];
            $resp = $this->llm->generate($prompt, /*forceNonStream=*/$options); // pastikan LlmService mengirim stream:false

            // Ekstraksi JSON yang robust (brace counter + bersihkan code fences)
            $resp = $this->llm->generate($prompt, $options);
            $resp = $this->cleanJsonString($resp);

            // Pastikan string diawali { dan diakhiri }
            if ($resp !== '' && $resp[0] !== '{') {
                $resp = '{' . trim($resp, ", \n\r\t") . '}';
            }

            $json = json_decode($resp, true);
            if ($json === null) {
                Log::error("JSON parse error: ".json_last_error_msg()." raw=[$resp]");
            }
            $obj = $json;
            if (!is_array($obj) || empty($obj['sql'])) {
                echo $this->ndjson(['error'=>'Empty or invalid SQL JSON','raw'=>$this->clip($resp)]);
                return;
            }

            // ===== B) Sanitize SQL =====
            try {
                $sql = $this->agent->sanitizeSql(trim($obj['sql']));
            } catch (\Throwable $e) {
                echo $this->ndjson(['phase'=>'plan','sql'=>trim($obj['sql']),'error'=>$e->getMessage()]);
                return;
            }

            // ===== C) Emit fase plan =====
            echo $this->ndjson([
                'phase' => 'plan',
                'sql'   => $sql,
                'notes' => $obj['notes'] ?? null
            ]);

            // ===== D) Eksekusi ClickHouse =====
            try {
                $rows = $this->ch->select($sql);
            } catch (\Throwable $e) {
                echo $this->ndjson(['phase'=>'query','error'=>$e->getMessage()]);
                return;
            }
            $preview = array_slice($rows, 0, 20);

            // ===== E) Stream jawaban LLM namun DINORMALISASI ke NDJSON =====
            $finalPrompt = "Question: {$question}\n"
                . "SQL: {$sql}\n"
                . "First rows (up to 20): " . json_encode($preview, JSON_UNESCAPED_UNICODE)
                . "\nAnswer the question first, analyze the question, and then based on the rows provided, gives answer to the question, you can refer to the rows if must, but prioritize answering the best answer for the question.";

            // LlmService::streamGenerate harus memanggil upstream (Ollama/OpenAI) dengan stream:true
            // lalu mem-pass setiap chunk string ke callback ini.
            $this->llm->streamGenerate($finalPrompt, function (string $chunk) {
                // Normalisasi: dukung 3 kemungkinan
                // 1) NDJSON native Ollama: tiap baris JSON berisi {response:"..."} dan final {done:true}
                // 2) SSE OpenAI-compat: baris "data: {...}" + [DONE]
                // 3) (Edge) single JSON final tanpa stream

                foreach (preg_split("/\r\n|\n|\r/", $chunk) as $line) {
                    $line = trim($line);
                    if ($line === '') continue;

                    // SSE
                    if (stripos($line, 'data:') === 0) {
                        $payload = trim(substr($line, 5));
                        if ($payload === '[DONE]') {
                            // (opsional) kirim tanda selesai
                            echo $this->ndjson(['done'=>true]);
                            continue;
                        }
                        $obj = json_decode($payload, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $delta = $obj['choices'][0]['delta']['content']
                                ?? $obj['choices'][0]['message']['content']
                                ?? $obj['choices'][0]['text']
                                ?? null;
                            if ($delta !== null && $delta !== '') {
                                echo $this->ndjson(['response'=>$delta]);
                            }
                        }
                        continue;
                    }

                    // NDJSON dari Ollama native
                    $obj = json_decode($line, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        if (isset($obj['response'])) {
                            echo $this->ndjson(['response'=>$obj['response']]);
                        } elseif (!empty($obj['error'])) {
                            echo $this->ndjson(['error'=>$obj['error']]);
                        } elseif (!empty($obj['done'])) {
                            echo $this->ndjson(['done'=>true]);
                        }
                        continue;
                    }

                    // (jarang) single JSON utuh dikirim sekaligus di akhir
                    $tail = $this->extractJsonObject($line);
                    if ($tail) {
                        $o = json_decode($tail, true);
                        $text = $o['response']
                            ?? ($o['choices'][0]['message']['content'] ?? $o['choices'][0]['text'] ?? null);
                        if ($text) echo $this->ndjson(['response'=>$text]);
                    }
                }
            });

        }, 200, [
            'Content-Type'       => 'application/x-ndjson',
            'Cache-Control'      => 'no-cache, no-transform',
            'X-Accel-Buffering'  => 'no', // untuk Nginx
        ]);
    }

    private function ndjson(array $obj): string
    {
        return json_encode($obj, JSON_UNESCAPED_UNICODE) . "\n";
    }

    private function clip(string $s, int $max = 500): string
    {
        $s = trim($s);
        return mb_strlen($s) > $max ? (mb_substr($s, 0, $max) . '…') : $s;
    }

    private function cleanJsonString(string $resp): string
    {
        $resp = trim($resp);

        // hapus fence ```json ... ```
        if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/is', $resp, $m)) {
            $resp = $m[1];
        }

        // hapus sisa ``` di awal/akhir
        $resp = preg_replace('/^```|```$/m', '', $resp);

        return trim($resp);
    }

    /**
     * Ekstraksi objek JSON pertama yang valid dari sebuah string:
     * - Menghapus code fence ```json ... ```
     * - Menggunakan counter { } supaya tidak greedy.
     */
    private function extractJsonObject(string $text): ?string
    {
        $t = trim($text);

        // Bersihkan code fences jika ada
        if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/is', $t, $m)) {
            $t = $m[1];
        }

        // Cari objek JSON pertama dengan brace counting
        $start = strpos($t, '{');
        if ($start === false) return null;

        $level = 0; $inStr = false; $esc = false;
        $len = strlen($t);
        for ($i = $start; $i < $len; $i++) {
            $ch = $t[$i];

            if ($inStr) {
                if ($esc) { $esc = false; continue; }
                if ($ch === '\\') { $esc = true; continue; }
                if ($ch === '"') { $inStr = false; continue; }
                continue;
            }

            if ($ch === '"') { $inStr = true; continue; }
            if ($ch === '{') { $level++; }
            if ($ch === '}') {
                $level--;
                if ($level === 0) {
                    $json = substr($t, $start, $i - $start + 1);
                    return $json;
                }
            }
        }
        return null;
    }

    public function dashboard_aiagent(Request $request)
    {
        return view('aiagent.dashboard_aiagent');
    }
}
