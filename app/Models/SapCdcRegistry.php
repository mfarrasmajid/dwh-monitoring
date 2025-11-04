<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SapCdcRegistry extends Model
{
    protected $table = 'sap_cdc_registry';
    protected $primaryKey = 'id';
    public $timestamps = true; // table has created_at/updated_at

    protected $fillable = [
        'job_code',
        'service_name',
        'entity_name',
        'method',
        'schedule_type',
        'interval_minutes',
        'cron_expr',
        'initial_path',
        'delta_path',
        'sim_enabled',
        'sim_db',
        'sim_table',
        'qas_enabled',
        'qas_db',
        'qas_table',
        'prod_enabled',
        'prod_db',
        'prod_table',
        'sim_client',
        'qas_client',
        'prod_client',
        'sim_next_skip',
        'qas_next_skip',
        'prod_next_skip',
        'sim_initial_done',
        'qas_initial_done',
        'prod_initial_done',
        'state_json',
        'trigger_force_initial',
        'trigger_run_now',
        'sim_last_status',
        'sim_last_error',
        'qas_last_status',
        'qas_last_error',
        'prod_last_status',
        'prod_last_error',
        'sim_last_run',
        'sim_next_run',
        'qas_last_run',
        'qas_next_run',
        'prod_last_run',
        'prod_next_run',
        'is_enabled',
        'ck_conn_id',
        'log_conn_id',
        'sim_next_delta_url',
        'qas_next_delta_url',
        'prod_next_delta_url',
        'maxpagesize',
    ];

    protected $casts = [
        'interval_minutes'       => 'integer',
        'sim_enabled'            => 'boolean',
        'qas_enabled'            => 'boolean',
        'prod_enabled'           => 'boolean',
        'sim_next_skip'          => 'integer',
        'qas_next_skip'          => 'integer',
        'prod_next_skip'         => 'integer',
        'sim_initial_done'       => 'boolean',
        'qas_initial_done'       => 'boolean',
        'prod_initial_done'      => 'boolean',
        'state_json'             => 'array',
        'trigger_force_initial'  => 'boolean',
        'trigger_run_now'        => 'boolean',
        'sim_last_run'           => 'datetime',
        'sim_next_run'           => 'datetime',
        'qas_last_run'           => 'datetime',
        'qas_next_run'           => 'datetime',
        'prod_last_run'          => 'datetime',
        'prod_next_run'          => 'datetime',
        'is_enabled'             => 'boolean',
        'maxpagesize'            => 'integer',
        'created_at'             => 'datetime',
        'updated_at'             => 'datetime',
    ];
}