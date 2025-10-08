<?php

namespace App\Services;

use GuzzleHttp\Client as Http;

class LlmService
{
    private Http $http;
    private string $base;
    private string $model;

    public function __construct()
    {
        $this->http = new Http(['timeout' => 180]);
        $this->base = rtrim(\Config::get('values.OLLAMA_URL') ?: 'http://localhost:11434', '/');
        $this->model = \Config::get('values.OLLAMA_MODEL') ?: 'llama3.2:3b';
    }

    public function generate(string $prompt, array $options = []): string
    {
        $res = $this->http->post($this->base.'/api/generate', [
            'json' => [
                'model' => $this->model,
                'prompt' => $prompt,
                'stream' => false,
                'options' => $options + ['num_ctx'=>2048],
            ]
        ]);
        $data = json_decode($res->getBody()->getContents(), true);
        return trim($data['response'] ?? '');

    }

    public function streamGenerate($prompt, $onChunk, array $options = []): void
    {
        $res = $this->http->post($this->base.'/api/generate', [
            'stream' => true,
            'json' => [
                'model' => $this->model,
                'prompt' => $prompt,
                'stream' => true,
                'options' => $options + ['num_ctx'=>2048],
            ],
        ]);
        $body = $res->getBody();
        while (!$body->eof()) {
            $chunk = $body->read(8192);
            if ($chunk === '') { usleep(30000); continue; }
            $onChunk($chunk); // NDJSON baris-baris dari Ollama
        }
    }
}
