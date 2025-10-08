<?php

namespace App\Services;
use Illuminate\Support\Facades\Log;

class AiSqlAgent
{

    /** @var ClickHouseClient */
    private $ch;

    public function __construct(ClickHouseClient $ch)
    {
        $this->ch    = $ch;
    }

    public function proposeSql(string $question): array
    {
        $allowed     = $this->allowedDbs(); // contoh: ['_hcm','_corsec', ...]
        $limit       = (int) (\Config::get('values.AI_DEFAULT_LIMIT') ?: 50);
        $schema      = $this->ch->schemaSummary($allowed); // ringkasan: db.table + kolom-kolom
        $allowedTxt  = implode(', ', array_map(fn($s) => "'{$s}'", $allowed));

        // (opsional) sinonim istilah bisnis → kandidat DB/table/kolom
        $synonyms = [
            '_hcm' => ['karyawan','pegawai','nik','nip','jabatan','divisi','join date','gaji','absen','cuti','pelatihan'],
            '_corsec' => ['dokumen legal','akta','komisaris','rapat','risalah','izin','perizinan','entitas hukum'],
            // tambahkan…
        ];
        $synonymsTxt = json_encode($synonyms, JSON_UNESCAPED_UNICODE);

        $system = <<<TXT
You are a senior data analyst for ClickHouse.

GOAL
- Write exactly ONE ClickHouse SELECT query that answers the user question using **real tables** (columns already flattened; no JSON functions).

HARD RULES
- Only SELECT. Never use INSERT/UPDATE/DELETE/ALTER/CREATE/DROP/OPTIMIZE/SET/SYSTEM.
- Only query tables within these databases: [$allowedTxt].
- Always use fully-qualified table names: <database>.<table>.
- Prefer the single most relevant table(s). If multiple are needed, use JOIN or UNION ALL.
- Always include LIMIT $limit (even for non-aggregations). For pure aggregations, you may still keep LIMIT.
- Use precise WHERE filters derived from the question (IDs, text, dates). Avoid SELECT * unless necessary.
- Default time window: last 30 days if a plausible date/time column exists (e.g., created_at, updated_at, event_time, date).
- Alias columns clearly with AS when needed.
- If multiple similarly named columns exist across tables, choose the one that best matches the schema and synonyms.

OUTPUT FORMAT
Return strict JSON with keys exactly: "sql" and "notes".
- "sql": the final ClickHouse SQL (one statement).
- "notes": which database(s)/table(s) were chosen, key filters/columns, and assumptions.

SELECTION GUIDANCE (question terms → databases)
{$synonymsTxt}

SCHEMA SUMMARY (databases, tables, columns):
{$schema}

PATTERNS
-- Simple single-table filter
SELECT
  t.nik_tg,
  t.name
FROM _hcm.users AS t
LIMIT $limit;

Now, produce the final SQL for the user question.
TXT;

        \Log::info("[AI PROPOSE SQL] system prompt:\n".$system);
        \Log::info("[AI PROPOSE SQL] question: ".$question);

        return ['prompt' => $system, 'question' => $question, 'limit' => $limit, 'allowed' => $allowed];
    }

    public function sanitizeSql(string $sql): string
    {
        $s = strtolower($sql);
        if (!preg_match('/^\s*select\s/', $s)) {
            throw new \InvalidArgumentException('Only SELECT is allowed');
        }
        if (str_contains($sql, ';')) {
            throw new \InvalidArgumentException('Multiple statements not allowed');
        }
        // whitelist DB (kecuali 'raw' untuk raw.events)
        // $allowed = array_map('strtolower', $this->allowedDbs());
        // if (preg_match_all('/([a-z_][a-z0-9_]*)\./i', $sql, $m)) {
        //     foreach ($m[1] as $db) {
        //         $db = strtolower($db);
        //         if ($db !== 'raw' && !in_array($db, $allowed, true)) {
        //             throw new \InvalidArgumentException("Disallowed database referenced: $db");
        //         }
        //     }
        // }
        // LIMIT jika perlu
        if (!preg_match('/\slimit\s/i', $s) && !preg_match('/group\s+by|count\(/i', $s)) {
            $sql .= ' LIMIT '.(int)\Config::get('values.AI_DEFAULT_LIMIT') ?: 50;
        }
        return $sql;
    }

    private function allowedDbs(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', \Config::get('values.AI_ALLOWED_DBS') ?: ''))));
    }
}
