<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DwhIngestionRegistry extends Model
{
    protected $table = 'dwh_ingestion_registry';
    protected $primaryKey = 'id';
    public $timestamps = false; // table has no created_at/updated_at

    protected $fillable = [
        'enabled',
        'source_mysql_conn_id', 'target_ch_conn_id', 'pg_log_conn_id',
        'source_db', 'source_table',
        'target_db', 'target_table',
        'pk_col', 'version_col',
        'schedule_type', 'interval_minutes', 'cron_expr',
        'chunk_rows', 'max_parallel', 'tmp_dir', 'ndjson_prefix',
        'log_table', 'log_type', 'log_kategori',
        'last_watermark', 'next_run_at', 'last_run_at',
        'last_status', 'last_error',
    ];

    protected $casts = [
        'enabled'         => 'boolean',
        'interval_minutes'=> 'integer',
        'chunk_rows'      => 'integer',
        'max_parallel'    => 'integer',
        'last_watermark'  => 'datetime',
        'next_run_at'     => 'datetime',
        'last_run_at'     => 'datetime',
    ];
}
