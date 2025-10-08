<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ck2CkIngestion extends Model
{
    // Use your pgsql connection name (adjust if different)
    protected $connection = 'pgsql';

    // Include schema-qualified table for Postgres
    protected $table = 'public.ck2ck_ingestion_registry';

    public $timestamps = false; // table has no created_at/updated_at

    protected $fillable = [
        'enabled',
        'source_ch_conn_id', 'target_ch_conn_id', 'pg_log_conn_id',
        'src_database', 'src_sql',
        'target_db', 'target_table',
        'pk_cols', 'version_col',
        'schedule_type', 'interval_minutes', 'cron_expr',
        'parallel_slices', 'page_rows',
        'allow_drop_columns', 'truncate_before_load',
        'pre_sql', 'post_sql',
        'log_table', 'log_type', 'log_kategori',
        // status fields are updated by DAG; expose if you want to allow manual clear
        'last_run_at', 'next_run_at', 'last_status', 'last_error',
    ];

    protected $casts = [
        'enabled'               => 'boolean',
        'allow_drop_columns'    => 'boolean',
        'truncate_before_load'  => 'boolean',
        'interval_minutes'      => 'integer',
        'parallel_slices'       => 'integer',
        'page_rows'             => 'integer',
        'last_run_at'           => 'datetime',
        'next_run_at'           => 'datetime',
    ];
}
