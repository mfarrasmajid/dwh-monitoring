{{-- resources/views/admin/dwh_manage_datawarehouse_form.blade.php --}}
@extends('layouts.main')

@section('title', $row->exists ? "Edit Job #{$row->id}" : 'New Datawarehouse')

@section('toolbar')
<div class="app-header-wrapper d-flex align-items-center justify-content-around justify-content-lg-between flex-wrap gap-6 gap-lg-0 mb-6 mb-lg-0"
     data-kt-swapper="true"
     data-kt-swapper-mode="{default: 'prepend', lg: 'prepend'}"
     data-kt-swapper-parent="{default: '#kt_app_content_container', lg: '#kt_app_header_wrapper'}">

  <div class="d-flex flex-column justify-content-center">
    <h1 class="text-gray-900 fw-bold fs-2x mb-2">
      {{ $row->exists ? 'Edit Datawarehouse' : 'Add Datawarehouse' }}
    </h1>
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-base">
      <li class="breadcrumb-item text-muted">
        <a href="{{ url('/') }}" class="text-muted text-hover-danger">Portal</a>
      </li>
      <li class="breadcrumb-item text-muted">/</li>
      <li class="breadcrumb-item text-muted">
        <a href="{{ url('/admin/dashboard_admin') }}" class="text-muted text-hover-danger">Dashboard Admin</a>
      </li>
      <li class="breadcrumb-item text-muted">/</li>
      <li class="breadcrumb-item text-muted">
        <a href="{{ url('/admin/manage_datawarehouse') }}" class="text-muted text-hover-danger">Manage Datawarehouse</a>
      </li>
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
    @if (session('ok'))
      <div class="alert alert-success d-flex align-items-center p-5 mb-10">
        <span class="svg-icon svg-icon-2hx svg-icon-success me-3"></span>
        <div class="d-flex flex-column">
          <h6 class="mb-1 text-success">{{ session('ok') }}</h6>
        </div>
      </div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
      <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
        <span class="svg-icon svg-icon-2hx svg-icon-danger me-3"></span>
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
        action="{{ $row->exists ? route('update_datawarehouse', $row) : route('store_datawarehouse') }}"
        method="POST">
        @csrf
        @if($row->exists) @method('PUT') @endif

        <div class="card-header">
          <h3 class="card-title fw-bolder">
            {{ $row->exists ? 'Edit Datawarehouse #'.$row->id : 'Add Datawarehouse' }}
          </h3>
          <div class="card-toolbar"></div>
        </div>

        <div class="card-body py-5">
          <div class="row">

            {{-- Enabled --}}
            <div class="col-lg-12 mb-5">
              <input type="hidden" name="enabled" value="0">
              <label class="form-check form-switch form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox" name="enabled" value="1"
                  {{ old('enabled', (int)$row->enabled) ? 'checked' : '' }}>
                <span class="form-check-label fw-bold text-muted">Enabled</span>
              </label>
            </div>

            {{-- Connection IDs --}}
            <div class="col-lg-4">
              <div class="mb-5">
                <label class="form-label">Source ClickHouse Conn ID</label>
                <input type="text" name="source_ch_conn_id" class="form-control form-control-solid" required
                  value="{{ old('source_ch_conn_id', $row->source_ch_conn_id ?? 'clickhouse_mitratel')  }}">
                <small class="text-muted">Connection in Apache Airflow, e.g. <code>clickhouse_mitratel</code></small>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-5">
                <label class="form-label">Target ClickHouse Conn ID</label>
                <input type="text" name="target_ch_conn_id" class="form-control form-control-solid" required
                  value="{{ old('target_ch_conn_id', $row->target_ch_conn_id ?? 'clickhouse_dwh_mitratel') }}">
                <small class="text-muted">Connection in Apache Airflow, e.g. <code>clickhouse_dwh_mitratel</code></small>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-5">
                <label class="form-label">PG Log Conn ID</label>
                <input type="text" name="pg_log_conn_id" class="form-control form-control-solid" required
                  value="{{ old('pg_log_conn_id', $row->pg_log_conn_id ?? 'airflow_logs_mitratel') }}">
                <small class="text-muted">Datalake PostgreSQL Connection ID, e.g. <code>airflow_logs_mitratel</code></small>
              </div>
            </div>

            {{-- Source (DB + SQL) --}}
            <div class="col-lg-4">
              <div class="mb-5">
                <label class="form-label">Source Database</label>
                <input type="text" name="src_database" class="form-control form-control-solid" required
                  value="{{ old('src_database', $row->src_database ?? 'oneflux') }}">
                  <small class="text-muted">Default <code>oneflux</code></small>
              </div>
            </div>
            <div class="col-lg-8">
              <div class="mb-5">
                <label class="form-label">Source SQL (SELECT …)</label>
                <textarea name="src_sql" rows="4" class="form-control form-control-solid" required
                  placeholder="SELECT col1, col2 FROM db.table WHERE …">{{ old('src_sql', $row->src_sql) }}</textarea>
                <small class="text-muted">Columns & types here define the target schema.</small>
              </div>
            </div>

            {{-- Target --}}
            <div class="col-lg-4">
              <div class="mb-5">
                <label class="form-label">Target Database</label>
                <input type="text" name="target_db" class="form-control form-control-solid" required
                  value="{{ old('target_db', $row->target_db) }}">
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-5">
                <label class="form-label">Target Table</label>
                <input type="text" name="target_table" class="form-control form-control-solid" required
                  value="{{ old('target_table', $row->target_table) }}">
              </div>
            </div>

            {{-- Keys & version --}}
            <div class="col-lg-6">
              <div class="mb-5">
                <label class="form-label">PK Columns (comma separated)</label>
                <input type="text" name="pk_cols" class="form-control form-control-solid" required
                  placeholder="id or ticket_id,site_id"
                  value="{{ old('pk_cols', $row->pk_cols) }}">
                <small class="text-muted">Used for <code>ORDER BY</code> (composite key supported).</small>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-5">
                <label class="form-label">Version Column (optional)</label>
                <input type="text" name="version_col" class="form-control form-control-solid"
                  placeholder="modified"
                  value="{{ old('version_col', $row->version_col) }}">
                <small class="text-muted">If set, engine is <code>ReplacingMergeTree(version)</code>.</small>
              </div>
            </div>

            {{-- Scheduling --}}
            <div class="col-lg-4">
              <div class="mb-5">
                <label class="form-label">Schedule Type</label>
                @php $sched = old('schedule_type', $row->schedule_type ?? 'interval'); @endphp
                <select name="schedule_type" id="schedule_type" class="form-select form-select-solid" required>
                  <option value="interval" {{ $sched==='interval' ? 'selected' : '' }}>Interval</option>
                  <option value="cron"     {{ $sched==='cron' ? 'selected' : '' }}>Cron</option>
                </select>
              </div>
            </div>
            <div class="col-lg-4" id="interval_group">
              <div class="mb-5">
                <label class="form-label">Interval Minutes</label>
                <input type="number" min="1" max="10080" name="interval_minutes"
                       class="form-control form-control-solid"
                       value="{{ old('interval_minutes', $row->interval_minutes ?? 15) }}">
              </div>
            </div>
            <div class="col-lg-4 hidden" id="cron_group">
              <div class="mb-5">
                <label class="form-label">Cron Expression</label>
                <input type="text" name="cron_expr" class="form-control form-control-solid"
                       placeholder="*/15 * * * *"
                       value="{{ old('cron_expr', $row->cron_expr) }}">
              </div>
            </div>

            {{-- Performance knobs --}}
            <div class="col-lg-3">
              <div class="mb-5">
                <label class="form-label">Parallel Slices</label>
                <input type="number" min="1" max="128" name="parallel_slices" class="form-control form-control-solid"
                  value="{{ old('parallel_slices', $row->parallel_slices ?? 4) }}">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-5">
                <label class="form-label">Page Rows</label>
                <input type="number" min="1" max="1000000" name="page_rows" class="form-control form-control-solid"
                  value="{{ old('page_rows', $row->page_rows ?? 50000) }}">
              </div>
            </div>

            {{-- Behaviors --}}
            <div class="col-lg-3">
              <div class="mb-5 mt-10">
                <input type="hidden" name="allow_drop_columns" value="0">
                <label class="form-check form-switch form-check-custom form-check-solid">
                  <input class="form-check-input" type="checkbox" name="allow_drop_columns" value="1"
                    {{ old('allow_drop_columns', (int)$row->allow_drop_columns) ? 'checked' : '1' }}>
                  <span class="form-check-label fw-bold text-muted">Allow Drop Columns</span>
                </label>
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-5 mt-10">
                <input type="hidden" name="truncate_before_load" value="0">
                <label class="form-check form-switch form-check-custom form-check-solid">
                  <input class="form-check-input" type="checkbox" name="truncate_before_load" value="1"
                    {{ old('truncate_before_load', (int)$row->truncate_before_load) ? 'checked' : '' }}>
                  <span class="form-check-label fw-bold text-muted">Truncate Before Load</span>
                </label>
              </div>
            </div>

            {{-- Hooks & Logging --}}
            <div class="col-lg-6">
              <div class="mb-5">
                <label class="form-label">Pre SQL (target)</label>
                <textarea name="pre_sql" rows="3" class="form-control form-control-solid"
                  placeholder="Optional SQL before load">{{ old('pre_sql', $row->pre_sql) }}</textarea>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-5">
                <label class="form-label">Post SQL (target)</label>
                <textarea name="post_sql" rows="3" class="form-control form-control-solid"
                  placeholder="Optional SQL after load">{{ old('post_sql', $row->post_sql) }}</textarea>
              </div>
            </div>

            <div class="col-lg-4">
              <div class="mb-5">
                <label class="form-label">Log Table</label>
                <input type="text" name="log_table" class="form-control form-control-solid" required
                  placeholder="airflow_logs"
                  value="{{ old('log_table', $row->log_table ?? 'airflow_logs') }}">
              </div>
            </div>
            <div class="col-lg-4">
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
                  value="{{ old('log_kategori', $row->log_kategori ?? 'Data Warehouse') }}">
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

        <div class="card-footer d-flex justify-content-between">
          <a href="{{ route('manage_datawarehouse') }}" class="btn btn-secondary">Back</a>
          <button type="submit" class="btn btn-danger">
            {{ $row->exists ? 'Update' : 'Create' }}
          </button>
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
    if (!intervalG || !cronG) return;
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
