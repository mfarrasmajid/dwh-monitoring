<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ck2PgIngestion extends Model
{
    // Use your Postgres connection; change if yours differs
    protected $connection = 'pgsql';

    // Schema-qualified table (Postgres)
    protected $table = 'public.ck2pg_ingestion_registry';

    public $timestamps = false; // registry table doesn’t have created_at/updated_at

    protected $fillable = [
        'enabled',
        'source_ch_conn_id',
        'source_sql',          // REQUIRED (query-only)
        'pk_value',
        'target_pg_conn_id', 'target_schema', 'target_table',
        'cursor_col',
        'schedule_type', 'interval_minutes', 'cron_expr',
        'chunk_rows', 'max_parallel', 'drop_extra_columns',
        'copy_timeout_seconds',
        'insert_page_size', 'commit_every_chunks',
        'pg_log_conn_id', 'log_table', 'log_type', 'log_kategori',
        'next_run_at', 'last_run_at', 'last_status', 'last_error',
    ];

    protected $casts = [
        'enabled'             => 'boolean',
        'drop_extra_columns'  => 'boolean',
        'interval_minutes'    => 'integer',
        'chunk_rows'          => 'integer',
        'max_parallel'        => 'integer',
        'copy_timeout_seconds'=> 'integer',
        'insert_page_size'    => 'integer',
        'commit_every_chunks' => 'integer',
        'last_run_at'         => 'datetime',
        'next_run_at'         => 'datetime',
    ];
}
