{{-- resources/views/admin/dwh_ingestion_registry_form.blade.php --}}
@extends('layouts.main')

@section('title', 'Ingestion Registry')

@section('toolbar')
<div class="app-header-wrapper d-flex align-items-center justify-content-around justify-content-lg-between flex-wrap gap-6 gap-lg-0 mb-6 mb-lg-0"
     data-kt-swapper="true"
     data-kt-swapper-mode="{default: 'prepend', lg: 'prepend'}"
     data-kt-swapper-parent="{default: '#kt_app_content_container', lg: '#kt_app_header_wrapper'}">

  <div class="d-flex flex-column justify-content-center">
    <h1 class="text-gray-900 fw-bold fs-2x mb-2">
      {{ isset($data['id']) ? 'Edit Ingestion' : 'Add Ingestion' }}
    </h1>
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-base">
      <li class="breadcrumb-item text-muted"><a href="{{ url('/') }}" class="text-muted text-hover-danger">Portal</a></li>
      <li class="breadcrumb-item text-muted">/</li>
      <li class="breadcrumb-item text-muted"><a href="{{ url('/admin/dashboard_admin') }}" class="text-muted text-hover-danger">Dashboard Admin</a></li>
      <li class="breadcrumb-item text-muted">/</li>
      <li class="breadcrumb-item text-muted"><a href="{{ url('/admin/ingestion_registry') }}" class="text-muted text-hover-danger">Ingestion Registry</a></li>
      <li class="breadcrumb-item text-muted">/</li>
      <li class="breadcrumb-item text-muted">{{ isset($data['id']) ? 'Edit' : 'Add' }}</li>
    </ul>
  </div>

  <div class="d-flex gap-3 gap-lg-8 flex-wrap"></div>
</div>
@stop

@section('content')
<div class="row g-5">
  <div class="col-md-12">

    {{-- Flash success --}}
    @if (session('success'))
      <div class="alert alert-success d-flex align-items-center p-5 mb-10">
        <span class="svg-icon svg-icon-2hx svg-icon-success me-3">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"/><path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"/></svg>
        </span>
        <div class="d-flex flex-column">
          <h6 class="mb-1 text-success">{{ session('success') }}</h6>
        </div>
      </div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
      <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
        <span class="svg-icon svg-icon-2hx svg-icon-danger me-3">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><rect x="9" y="13.0283" width="7.3536" height="1.2256" rx="0.6128" transform="rotate(-45 9 13.0283)" fill="currentColor"/><rect x="9.86664" y="7.93359" width="7.3536" height="1.2256" rx="0.6128" transform="rotate(45 9.86664 7.93359)" fill="currentColor"/></svg>
        </span>
        <div class="d-flex flex-column">
          @foreach ($errors->all() as $error)
            <h6 class="mb-1 text-danger">{{ $error }}</h6>
          @endforeach
        </div>
        <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
          <i class="bi bi-x fs-1 text-danger"></i>
        </button>
      </div>
    @endif

    {{-- Error flash --}}
    @if (session('error'))
      <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
        <span class="svg-icon svg-icon-2hx svg-icon-danger me-3"></span>
        <div class="d-flex flex-column">
          <h6 class="mb-1 text-danger">{{ session('error') }}</h6>
        </div>
      </div>
    @endif

    <div class="card card-flush shadow-sm p-5" id="detail_form">
      <form
        @if(isset($data['id']))
          action="{{ url('/admin/ingestion_registry') }}/{{ $data['id'] }}"
        @else
          action="{{ url('/admin/ingestion_registry') }}"
        @endif
        method="POST">
        @csrf
        @if(isset($data['id'])) @method('PUT') @endif

        <div class="card-header">
          <h3 class="card-title fw-bolder">
            {{ isset($data['id']) ? 'Edit Ingestion #' . $data['id'] : 'Add Ingestion' }}
          </h3>
          <div class="card-toolbar"></div>
        </div>

        @php
          $row = $data['row'] ?? null;
        @endphp

        <div class="card-body py-5">
          <div class="row">

            {{-- Enabled --}}
            <div class="col-lg-12 mb-5">
              <label class="form-check form-switch form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox" name="enabled" value="1"
                  {{ old('enabled', isset($row) ? (int)$row->enabled : 1) ? 'checked' : '' }}>
                <span class="form-check-label fw-bold text-muted">Enabled</span>
              </label>
            </div>

            {{-- Connection IDs --}}
            <div class="col-lg-4">
              <div class="mb-5">
                <label class="form-label">Source MySQL Conn ID</label>
                <input type="text" name="source_mysql_conn_id" class="form-control form-control-solid" required
                  value="{{ old('source_mysql_conn_id', $row->source_mysql_conn_id ?? 'db_oneflux_tms') }}">
                <small class="text-muted">OneFlux Connection ID, e.g. <code>db_oneflux_tms</code></small>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-5">
                <label class="form-label">Target ClickHouse Conn ID</label>
                <input type="text" name="target_ch_conn_id" class="form-control form-control-solid" required
                  value="{{ old('target_ch_conn_id', $row->target_ch_conn_id ?? 'clickhouse_mitratel') }}">
                  <small class="text-muted">Datalake Clickhouse Connection ID, e.g. <code>clickhouse_mitratel</code></small>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-5">
                <label class="form-label">PG Log Conn ID</label>
                <input type="text" name="pg_log_conn_id" class="form-control form-control-solid"
                  value="{{ old('pg_log_conn_id', $row->pg_log_conn_id ?? 'airflow_logs_mitratel') }}">
                  <small class="text-muted">Datalake PostgreSQL Connection ID, e.g. <code>airflow_logs_mitratel</code></small>
              </div>
            </div>

            {{-- Source --}}
            <div class="col-lg-6">
              <div class="mb-5">
                <label class="form-label">Source DB</label>
                <input type="text" name="source_db" class="form-control form-control-solid" required
                  value="{{ old('source_db', $row->source_db ?? 'db_tms') }}">
                  <small class="text-muted">Default <code>db_tms</code></small>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-5">
                <label class="form-label">Source Table</label>
                <input type="text" name="source_table" class="form-control form-control-solid" required
                  value="{{ old('source_table', $row->source_table ?? '') }}">
                  <small class="text-muted">OneFlux Table, e.g. <code>tabMaintenance Order</code></small>
              </div>
            </div>

            {{-- Target --}}
            <div class="col-lg-6">
              <div class="mb-5">
                <label class="form-label">Target DB</label>
                <input type="text" name="target_db" class="form-control form-control-solid" required
                  value="{{ old('target_db', $row->target_db ?? '') }}">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-5">
                <label class="form-label">Target Table</label>
                <input type="text" name="target_table" class="form-control form-control-solid" required
                  value="{{ old('target_table', $row->target_table ?? '') }}">
              </div>
            </div>

            {{-- Keys & version --}}
            <div class="col-lg-6">
              <div class="mb-5">
                <label class="form-label">Primary Key Column</label>
                <input type="text" name="pk_col" class="form-control form-control-solid" required
                  value="{{ old('pk_col', $row->pk_col ?? 'name') }}">
                <small class="text-muted">Used for ORDER BY / PRIMARY KEY in ClickHouse.</small>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-5">
                <label class="form-label">Version Column (optional)</label>
                <input type="text" name="version_col" class="form-control form-control-solid"
                  value="{{ old('version_col', $row->version_col ?? 'modified') }}">
                <small class="text-muted">If provided, engine uses ReplacingMergeTree(version_v) & incremental pull by this column.</small>
              </div>
            </div>

            {{-- Scheduling --}}
            <div class="col-lg-4">
              <div class="mb-5">
                <label class="form-label">Schedule Type</label>
                <select name="schedule_type" id="schedule_type" class="form-select form-select-solid" required>
                  @php $sched = old('schedule_type', $row->schedule_type ?? 'interval'); @endphp
                  <option value="interval" {{ $sched==='interval' ? 'selected' : '' }}>Interval</option>
                  <option value="cron"     {{ $sched==='cron' ? 'selected' : '' }}>Cron</option>
                </select>
              </div>
            </div>
            <div class="col-lg-4" id="interval_group">
              <div class="mb-5">
                <label class="form-label">Interval Minutes</label>
                <input type="number" min="1" name="interval_minutes" class="form-control form-control-solid"
                  value="{{ old('interval_minutes', $row->interval_minutes ?? 15) }}">
              </div>
            </div>
            <div class="col-lg-4" id="cron_group">
              <div class="mb-5">
                <label class="form-label">Cron Expression</label>
                <input type="text" name="cron_expr" class="form-control form-control-solid"
                  value="{{ old('cron_expr', $row->cron_expr ?? '') }}" placeholder="*/5 * * * *">
              </div>
            </div>

            {{-- Performance knobs --}}
            <div class="col-lg-4">
              <div class="mb-5">
                <label class="form-label">Chunk Rows</label>
                <input type="number" min="1000" step="1000" name="chunk_rows" class="form-control form-control-solid"
                  value="{{ old('chunk_rows', $row->chunk_rows ?? 10000) }}">
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-5">
                <label class="form-label">Max Parallel</label>
                <input type="number" min="1" max="16" name="max_parallel" class="form-control form-control-solid"
                  value="{{ old('max_parallel', $row->max_parallel ?? 4) }}">
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-5">
                <label class="form-label">Temp Dir</label>
                <input type="text" name="tmp_dir" class="form-control form-control-solid"
                  value="{{ old('tmp_dir', $row->tmp_dir ?? '/tmp') }}">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-5">
                <label class="form-label">NDJSON Prefix</label>
                <input type="text" name="ndjson_prefix" class="form-control form-control-solid"
                  value="{{ old('ndjson_prefix', $row->ndjson_prefix ?? 'DL_generic_') }}">
              </div>
            </div>

            {{-- Logging --}}
            <div class="col-lg-6">
              <div class="mb-5">
                <label class="form-label">Log Table</label>
                <input type="text" name="log_table" class="form-control form-control-solid"
                  value="{{ old('log_table', $row->log_table ?? 'airflow_logs') }}">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-5">
                <label class="form-label">Log Type</label>
                <input type="text" name="log_type" class="form-control form-control-solid"
                  value="{{ old('log_type', $row->log_type ?? 'incremental') }}">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-5">
                <label class="form-label">Log Kategori</label>
                <input type="text" name="log_kategori" class="form-control form-control-solid"
                  value="{{ old('log_kategori', $row->log_kategori ?? 'Data Lake') }}">
              </div>
            </div>

            {{-- Read-only bookkeeping (optional show) --}}
            @if(isset($row))
              <div class="col-lg-12">
                <div class="alert alert-secondary">
                  <div class="d-flex flex-wrap gap-4">
                    <div><strong>Last Status:</strong> {{ $row->last_status ?? '-' }}</div>
                    <div><strong>Last Run:</strong> {{ $row->last_run_at ?? '-' }}</div>
                    <div><strong>Next Run:</strong> {{ $row->next_run_at ?? '-' }}</div>
                    <div class="w-100"></div>
                    <div><strong>Last Error:</strong> {{ $row->last_error ?? '-' }}</div>
                  </div>
                </div>
              </div>
            @endif

          </div>
        </div>

        <div class="card-footer text-end">
          <a href="{{ url('/admin/ingestion_registry') }}" class="btn btn-secondary me-5">Back</a>
          <input type="submit" class="btn btn-danger" value="Submit"/>
        </div>
      </form>
    </div>

  </div>
</div>
@stop

@section('styles')
<style>.hidden{display:none;}</style>
@stop

@section('scripts')
<script>
  function toggleScheduleGroups() {
    const st = document.getElementById('schedule_type')?.value || 'interval';
    const intervalG = document.getElementById('interval_group');
    const cronG = document.getElementById('cron_group');
    if (st === 'interval') {
      intervalG.classList.remove('hidden');
      cronG.classList.add('hidden');
    } else {
      cronG.classList.remove('hidden');
      intervalG.classList.add('hidden');
    }
  }
  document.addEventListener('DOMContentLoaded', function() {
    toggleScheduleGroups();
    document.getElementById('schedule_type')?.addEventListener('change', toggleScheduleGroups);
  });
</script>
@stop
