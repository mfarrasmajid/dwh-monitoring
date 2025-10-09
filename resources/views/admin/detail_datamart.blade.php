{{-- resources/views/admin/detail_datamart.blade.php --}}
@extends('layouts.main')

@section('title', $row->exists ? "Edit Datamart Job #{$row->id}" : 'New Datamart')

@section('toolbar')
<div class="app-header-wrapper d-flex align-items-center justify-content-around justify-content-lg-between flex-wrap gap-6 gap-lg-0 mb-6 mb-lg-0"
     data-kt-swapper="true"
     data-kt-swapper-mode="{default: 'prepend', lg: 'prepend'}"
     data-kt-swapper-parent="{default: '#kt_app_content_container', lg: '#kt_app_header_wrapper'}">

  <div class="d-flex flex-column justify-content-center">
    <h1 class="text-gray-900 fw-bold fs-2x mb-2">
      {{ $row->exists ? 'Edit Datamart Ingestion' : 'Add Datamart Ingestion' }}
    </h1>
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-base">
      <li class="breadcrumb-item text-muted"><a href="{{ url('/') }}" class="text-muted text-hover-danger">Portal</a></li>
      <li class="breadcrumb-item text-muted">/</li>
      <li class="breadcrumb-item text-muted"><a href="{{ url('/admin/dashboard_admin') }}" class="text-muted text-hover-danger">Dashboard Admin</a></li>
      <li class="breadcrumb-item text-muted">/</li>
      <li class="breadcrumb-item text-muted"><a href="{{ route('manage_datamart') }}" class="text-muted text-hover-danger">Manage Datamart</a></li>
      <li class="breadcrumb-item text-muted">/</li>
      <li class="breadcrumb-item text-muted">{{ $row->exists ? 'Edit' : 'Add' }}</li>
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
        <div class="d-flex flex-column">
          <h6 class="mb-1 text-success">{{ session('success') }}</h6>
        </div>
        <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
          <i class="bi bi-x fs-1 text-success"></i>
        </button>
      </div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
      <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
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
        <div class="d-flex flex-column">
          <h6 class="mb-1 text-danger">{{ session('error') }}</h6>
        </div>
      </div>
    @endif

    <div class="card card-flush shadow-sm p-5" id="detail_form">
      <form
        action="{{ $row->exists ? route('update_datamart', $row->id) : route('store_datamart') }}"
        method="POST">
        @csrf
        @if($row->exists) @method('PUT') @endif

        <div class="card-header">
          <h3 class="card-title fw-bolder">
            {{ $row->exists ? 'Edit Datamart Ingestion #' . $row->id : 'Add Datamart Ingestion' }}
          </h3>
          <div class="card-toolbar"></div>
        </div>

        <div class="card-body py-5">
          <div class="row">

            {{-- Enabled --}}
            <div class="col-lg-12 mb-5">
              <label class="form-check form-switch form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox" name="enabled" value="1"
                  {{ old('enabled', $row->exists ? (int)$row->enabled : 1) ? 'checked' : '' }}>
                <span class="form-check-label fw-bold text-muted">Enabled</span>
              </label>
            </div>

            {{-- Source (ClickHouse) --}}
            <div class="col-lg-4">
              <div class="mb-5">
                <label class="form-label">Source CH Conn ID</label>
                <input type="text" name="source_ch_conn_id" class="form-control form-control-solid" required
                  value="{{ old('source_ch_conn_id', $row->source_ch_conn_id ?? 'clickhouse_dwh_mitratel') }}">
                <small class="text-muted">Connection in Apache Airflow, e.g. <code>clickhouse_dwh_mitratel</code></small>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-5">
                <label class="form-label">Target PG Conn ID</label>
                <input type="text" name="target_pg_conn_id" class="form-control form-control-solid" required
                  value="{{ old('target_pg_conn_id', $row->target_pg_conn_id ?? 'db_datamart_mitratel') }}">
                <small class="text-muted">Connection in Apache Airflow, e.g. <code>db_datamart_mitratel</code></small>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-5">
                <label class="form-label">PG Log Conn ID</label>
                <input type="text" name="pg_log_conn_id" class="form-control form-control-solid" required
                  value="{{ old('pg_log_conn_id', $row->pg_log_conn_id ?? 'airflow_logs_mitratel') }}">
              </div>
            </div>
            <div class="col-lg-12">
                <div class="mb-5">
                    <label class="form-label">Source SQL</label>
                    <textarea name="source_sql" class="form-control form-control-solid" rows="6">{{ old('source_sql', $row->source_sql ?? '') }}</textarea>
                    <div class="form-text">This SQL defines the columns used to create/sync the target table.</div>
                </div>
            </div>

            {{-- Target (PostgreSQL) --}}
            
            <div class="col-lg-3">
              <div class="mb-5">
                <label class="form-label">Target Schema</label>
                <input type="text" name="target_schema" class="form-control form-control-solid" required
                  value="{{ old('target_schema', $row->target_schema ?? 'public') }}">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-5">
                <label class="form-label">Target Table</label>
                <input type="text" name="target_table" class="form-control form-control-solid" required
                  value="{{ old('target_table', $row->target_table ?? '') }}">
              </div>
            </div>
            <div class="col-lg-3">
                <div class="mb-5">
                    <label class="form-label">PK Columns (comma-separated)</label>
                    <input name="pk_value" class="form-control form-control-solid" placeholder="e.g. id or id,name"
                        value="{{ old('pk_value', $row->pk_value ?? '') }}">
                    <div class="form-text">Must be present in the query’s SELECT list.</div>
                </div>
            </div>
            {{-- Cursor (optional) --}}
            <div class="col-lg-3">
              <div class="mb-5">
                <label class="form-label">Cursor Column (optional)</label>
                <input type="text" name="cursor_col" class="form-control form-control-solid"
                  value="{{ old('cursor_col', $row->cursor_col ?? '') }}" placeholder="modified">
                <small class="text-muted">Used for incremental ordering when present.</small>
              </div>
            </div>

            {{-- Scheduling --}}
            @php $sched = old('schedule_type', $row->schedule_type ?? 'interval'); @endphp
            <div class="col-lg-4">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-5">
                    <label class="form-label">Schedule Type</label>
                    <select name="schedule_type" id="schedule_type" class="form-select form-select-solid" required>
                      <option value="interval" {{ $sched==='interval' ? 'selected' : '' }}>Interval</option>
                      <option value="cron"     {{ $sched==='cron' ? 'selected' : '' }}>Cron</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6" id="interval_group">
                  <div class="mb-5">
                    <label class="form-label">Interval Minutes</label>
                    <input type="number" min="1" name="interval_minutes" class="form-control form-control-solid"
                      value="{{ old('interval_minutes', $row->interval_minutes ?? 30) }}">
                  </div>
                </div>
                <div class="col-md-6" id="cron_group">
                  <div class="mb-5">
                    <label class="form-label">Cron Expression</label>
                    <input type="text" name="cron_expr" class="form-control form-control-solid"
                      value="{{ old('cron_expr', $row->cron_expr ?? '') }}" placeholder="*/30 * * * *">
                  </div>
                </div>
              </div>
            </div>

            {{-- Performance / behavior --}}
            <div class="col-lg-2">
              <div class="mb-5">
                <label class="form-label">Chunk Rows</label>
                <input type="number" min="100" step="100" name="chunk_rows" class="form-control form-control-solid"
                  value="{{ old('chunk_rows', $row->chunk_rows ?? 100000) }}">
              </div>
            </div>
            <div class="col-lg-2">
              <div class="mb-5">
                <label class="form-label">Max Parallel</label>
                <input type="number" min="1" max="128" name="max_parallel" class="form-control form-control-solid"
                  value="{{ old('max_parallel', $row->max_parallel ?? 4) }}">
              </div>
            </div>
            <div class="col-lg-2">
              <div class="mb-5">
                <label class="form-label">Timeout (seconds)</label>
                <input type="number" min="60" max="86400" name="copy_timeout_seconds" class="form-control form-control-solid"
                  value="{{ old('copy_timeout_seconds', $row->copy_timeout_seconds ?? 7200) }}">
              </div>
            </div>
            <div class="col-lg-2">
              <div class="mb-5">
                <label class="form-label">Drop Extra Columns</label>
                <label class="form-check form-switch form-check-custom form-check-solid">
                  <input class="form-check-input" type="checkbox" name="drop_extra_columns" value="1"
                    {{ old('drop_extra_columns', $row->exists ? (int)$row->drop_extra_columns : 1) ? 'checked' : '' }}>
                  <span class="form-check-label fw-bold text-muted">Auto sync schema (drop extras on PG)</span>
                </label>
              </div>
            </div>
            {{-- Insert Page Size --}}
            <div class="col-lg-2">
              <div class="mb-5">
                <label class="form-label">Insert Page Size</label>
                <input
                  type="number"
                  min="100"
                  step="100"
                  name="insert_page_size"
                  class="form-control form-control-solid"
                  value="{{ old('insert_page_size', $row->insert_page_size ?? 20000) }}"
                >
                <div class="form-text">Rows per INSERT batch into PostgreSQL.</div>
              </div>
            </div>

            {{-- Commit Every Chunk --}}
            <div class="col-lg-2">
              <div class="mb-5">
                <label class="form-label">Commit Every Chunk</label>
                <input
                  type="number"
                  min="1"
                  name="commit_every_chunk"
                  class="form-control form-control-solid"
                  value="{{ old('commit_every_chunk', $row->commit_every_chunk ?? 25) }}"
                >
                <div class="form-text">How many insert batches before calling commit.</div>
              </div>
            </div>

            {{-- Logging --}}
            
            <div class="col-lg-2">
              <div class="mb-5">
                <label class="form-label">Log Table</label>
                <input type="text" name="log_table" class="form-control form-control-solid" required
                  value="{{ old('log_table', $row->log_table ?? 'airflow_logs') }}">
              </div>
            </div>
            <div class="col-lg-2">
              <div class="mb-5">
                <label class="form-label">Log Type</label>
                <input type="text" name="log_type" class="form-control form-control-solid" required
                  value="{{ old('log_type', $row->log_type ?? 'incremental') }}">
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-5">
                <label class="form-label">Log Kategori</label>
                <input type="text" name="log_kategori" class="form-control form-control-solid" required
                  value="{{ old('log_kategori', $row->log_kategori ?? 'Data Mart') }}">
              </div>
            </div>

            {{-- Read-only bookkeeping --}}
            @if($row->exists)
              <div class="col-lg-12">
                <div class="alert alert-info">
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
          <a href="{{ route('manage_datamart') }}" class="btn btn-secondary me-5">Back</a>
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
