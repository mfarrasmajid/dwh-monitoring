<?php
    return [
        'APP_ENV' => env('APP_ENV', null),
        'ASSET_URL' => env('ASSET_URL', null),
        'WHATSAPP_API_URL' => env('WHATSAPP_API_URL', null),
        'WHATSAPP_API_USER' => env('WHATSAPP_API_USER', null),
        'WHATSAPP_TOKEN' => env('WHATSAPP_TOKEN', null),

        'APIV2_URL' => env('APIV2_URL', null),
        'APIV2_KEY' => env('APIV2_KEY', null),
        'CLICKHOUSE_HOST' => env('CLICKHOUSE_HOST', '127.0.0.1'),
        'CLICKHOUSE_PORT' => env('CLICKHOUSE_PORT', 8123),
        'CLICKHOUSE_DB' => env('CLICKHOUSE_DB', 'default'),
        'CLICKHOUSE_USER' => env('CLICKHOUSE_USER', 'readonly'),
        'CLICKHOUSE_PASS' => env('CLICKHOUSE_PASS', ''),
        'CLICKHOUSE_TIMEOUT' => env('CLICKHOUSE_TIMEOUT', 60),
        'OLLAMA_URL' => env('OLLAMA_URL', '127.0.0.1'),
        'OLLAMA_MODEL' => env('OLLAMA_MODEL', 'llama3.2:1b'),
        'AI_ALLOWED_DBS' => env('AI_ALLOWED_DBS', ''),
        'AI_DEFAULT_LIMIT' => env('AI_DEFAULT_LIMIT', 50),
        'RAW_EVENTS_TABLE' => env('RAW_EVENTS_TABLE', ''),
    ];
