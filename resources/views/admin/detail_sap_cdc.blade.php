@extends('layouts.main')

@section('title', isset($data['row']) ? "Edit SAP CDC #{$data['row']->id}" : 'New SAP CDC Job')

@section('toolbar')
<div class="app-header-wrapper d-flex align-items-center justify-content-around justify-content-lg-between flex-wrap gap-6 gap-lg-0 mb-6 mb-lg-0"
     data-kt-swapper="true"
     data-kt-swapper-mode="{default: 'prepend', lg: 'prepend'}"
     data-kt-swapper-parent="{default: '#kt_app_content_container', lg: '#kt_app_header_wrapper'}">

  <div class="d-flex flex-column justify-content-center">
    <h1 class="text-gray-900 fw-bold fs-2x mb-2">
      {{ isset($data['id']) ? 'Edit SAP CDC Job' : 'Add SAP CDC Job' }}
    </h1>
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-base">
      <li class="breadcrumb-item text-muted"><a href="{{ url('/') }}" class="text-muted text-hover-danger">Portal</a></li>
      <li class="breadcrumb-item text-muted">/</li>
      <li class="breadcrumb-item text-muted"><a href="{{ url('/admin/dashboard_admin') }}" class="text-muted text-hover-danger">Dashboard Admin</a></li>
      <li class="breadcrumb-item text-muted">/</li>
      <li class="breadcrumb-item text-muted"><a href="{{ route('manage_sap_cdc') }}" class="text-muted text-hover-danger">Manage SAP CDC</a></li>
      <li class="breadcrumb-item text-muted">/</li>
      <li class="breadcrumb-item text-muted">{{ isset($data['id']) ? 'Edit' : 'Add' }}</li>
    </ul>
  </div>
</div>
@stop

@section('content')
<div class="row g-5">
  <div class="col-md-12">

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
      </div>
    @endif

    <div class="card card-flush shadow-sm p-5">
      <form
        @if(isset($data['id']))
          action="{{ route('update_sap_cdc', $data['id']) }}"
        @else
          action="{{ route('store_sap_cdc') }}"
        @endif
        method="POST"
        id="mainForm">
        @csrf
        @if(isset($data['id'])) @method('PUT') @endif

        <div class="card-header">
          <h3 class="card-title fw-bolder">
            {{ isset($data['id']) ? 'Edit SAP CDC Job #' . $data['id'] : 'Add SAP CDC Job' }}
          </h3>
        </div>

        @php
          $row = $data['row'] ?? null;
        @endphp

        <div class="card-body py-5">
          
          {{-- Enabled --}}
          <div class="row mb-5">
            <div class="col-lg-12">
              <label class="form-check form-switch form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox" name="is_enabled" value="1"
                       {{ old('is_enabled', $row->is_enabled ?? true) ? 'checked' : '' }}>
                <span class="form-check-label fw-bold">Job Enabled</span>
              </label>
            </div>
          </div>

          {{-- Job Code --}}
          <div class="row mb-5">
            <label class="col-lg-3 col-form-label required fw-bold fs-6">Job Code</label>
            <div class="col-lg-9">
              <input type="text" name="job_code" class="form-control form-control-solid"
                     value="{{ old('job_code', $row->job_code ?? '') }}" required>
              <div class="form-text">Unique job identifier (e.g., SAP_CDC_AFIH)</div>
            </div>
          </div>

          {{-- Service Name --}}
          <div class="row mb-5">
            <label class="col-lg-3 col-form-label required fw-bold fs-6">Service Name</label>
            <div class="col-lg-9">
              <input type="text" name="service_name" class="form-control form-control-solid"
                     value="{{ old('service_name', $row->service_name ?? '') }}" required>
              <div class="form-text">SAP OData service name</div>
            </div>
          </div>

          {{-- Entity Name --}}
          <div class="row mb-5">
            <label class="col-lg-3 col-form-label required fw-bold fs-6">Entity Name</label>
            <div class="col-lg-9">
              <input type="text" name="entity_name" class="form-control form-control-solid"
                     value="{{ old('entity_name', $row->entity_name ?? '') }}" required>
              <div class="form-text">Entity name for DeltaLinksOf endpoint (e.g., ZCDC_AFIH_1)</div>
            </div>
          </div>

          {{-- Method --}}
          <div class="row mb-5">
            <label class="col-lg-3 col-form-label required fw-bold fs-6">Method</label>
            <div class="col-lg-9">
              <select name="method" class="form-select form-select-solid" required>
                <option value="continuous_cdc" {{ old('method', $row->method ?? 'continuous_cdc') == 'continuous_cdc' ? 'selected' : '' }}>Continuous CDC</option>
                <option value="weekly_refresh" {{ old('method', $row->method ?? '') == 'weekly_refresh' ? 'selected' : '' }}>Weekly Refresh</option>
              </select>
            </div>
          </div>

          {{-- Schedule Type --}}
          <div class="row mb-5">
            <label class="col-lg-3 col-form-label required fw-bold fs-6">Schedule Type</label>
            <div class="col-lg-9">
              <select name="schedule_type" class="form-select form-select-solid" id="schedule_type" required>
                <option value="interval" {{ old('schedule_type', $row->schedule_type ?? 'interval') == 'interval' ? 'selected' : '' }}>Interval</option>
                <option value="cron" {{ old('schedule_type', $row->schedule_type ?? '') == 'cron' ? 'selected' : '' }}>Cron</option>
              </select>
            </div>
          </div>

          {{-- Interval Minutes --}}
          <div class="row mb-5" id="interval_row">
            <label class="col-lg-3 col-form-label fw-bold fs-6">Interval (minutes)</label>
            <div class="col-lg-9">
              <input type="number" name="interval_minutes" class="form-control form-control-solid"
                     value="{{ old('interval_minutes', $row->interval_minutes ?? 5) }}" min="1">
            </div>
          </div>

          {{-- Cron Expression --}}
          <div class="row mb-5" id="cron_row" style="display:none;">
            <label class="col-lg-3 col-form-label fw-bold fs-6">Cron Expression</label>
            <div class="col-lg-9">
              <input type="text" name="cron_expr" class="form-control form-control-solid"
                     value="{{ old('cron_expr', $row->cron_expr ?? '') }}">
            </div>
          </div>

          {{-- Initial Path --}}
          <div class="row mb-5">
            <label class="col-lg-3 col-form-label required fw-bold fs-6">Initial Path</label>
            <div class="col-lg-9">
              <textarea name="initial_path" class="form-control form-control-solid" rows="2" required>{{ old('initial_path', $row->initial_path ?? '') }}</textarea>
            </div>
          </div>

          {{-- Delta Path --}}
          <div class="row mb-5">
            <label class="col-lg-3 col-form-label fw-bold fs-6">Delta Path</label>
            <div class="col-lg-9">
              <textarea name="delta_path" class="form-control form-control-solid" rows="2">{{ old('delta_path', $row->delta_path ?? '') }}</textarea>
            </div>
          </div>

          {{-- Max Page Size --}}
          <div class="row mb-5">
            <label class="col-lg-3 col-form-label fw-bold fs-6">Max Page Size</label>
            <div class="col-lg-9">
              <input type="number" name="maxpagesize" class="form-control form-control-solid"
                     value="{{ old('maxpagesize', $row->maxpagesize ?? 100000) }}" min="1000">
              <div class="form-text">Max page size for OData requests (odata.maxpagesize header)</div>
            </div>
          </div>

          <hr class="my-8">
          <h3 class="mb-5">Environment Configuration</h3>

          {{-- SIM Environment --}}
          <div class="card mb-5">
            <div class="card-header">
              <h4 class="card-title">
                <label class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="sim_enabled" value="1"
                         {{ old('sim_enabled', $row->sim_enabled ?? true) ? 'checked' : '' }}>
                  <span class="form-check-label fw-bold">SIM Environment</span>
                </label>
              </h4>
            </div>
            <div class="card-body">
              <div class="row mb-3">
                <label class="col-lg-3 col-form-label fw-bold">Database</label>
                <div class="col-lg-9">
                  <input type="text" name="sim_db" class="form-control form-control-solid"
                         value="{{ old('sim_db', $row->sim_db ?? 'sap_sim') }}">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-lg-3 col-form-label required fw-bold">Table</label>
                <div class="col-lg-9">
                  <input type="text" name="sim_table" class="form-control form-control-solid"
                         value="{{ old('sim_table', $row->sim_table ?? '') }}" required>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-lg-3 col-form-label fw-bold">Client</label>
                <div class="col-lg-9">
                  <input type="text" name="sim_client" class="form-control form-control-solid"
                         value="{{ old('sim_client', $row->sim_client ?? '300') }}">
                </div>
              </div>
              
              @if(isset($data['id']) && $row)
              <hr class="my-4">
              <div class="row mb-3">
                <label class="col-lg-3 col-form-label fw-bold">Last Status</label>
                <div class="col-lg-9">
                  @if($row->sim_last_status)
                    @if($row->sim_last_status === 'success')
                      <span class="badge bg-success fs-6">{{ $row->sim_last_status }}</span>
                    @elseif($row->sim_last_status === 'failed')
                      <span class="badge bg-danger fs-6">{{ $row->sim_last_status }}</span>
                    @else
                      <span class="badge bg-secondary fs-6">{{ $row->sim_last_status }}</span>
                    @endif
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </div>
              </div>
              
              @if($row->sim_last_error)
              <div class="row mb-3">
                <label class="col-lg-3 col-form-label fw-bold text-danger">Last Error</label>
                <div class="col-lg-9">
                  <div class="alert alert-danger p-3 mb-0">
                    <small>{{ $row->sim_last_error }}</small>
                  </div>
                </div>
              </div>
              @endif

              @if($row->sim_last_run)
              <div class="row mb-3">
                <label class="col-lg-3 col-form-label fw-bold">Last Run</label>
                <div class="col-lg-9">
                  <span class="text-muted">{{ $row->sim_last_run->format('Y-m-d H:i:s') }}</span>
                </div>
              </div>
              @endif

              @if($row->sim_next_run)
              <div class="row">
                <label class="col-lg-3 col-form-label fw-bold">Next Run</label>
                <div class="col-lg-9">
                  <span class="text-muted">{{ $row->sim_next_run->format('Y-m-d H:i:s') }}</span>
                </div>
              </div>
              @endif
              @endif
            </div>
          </div>

          {{-- QAS Environment --}}
          <div class="card mb-5">
            <div class="card-header">
              <h4 class="card-title">
                <label class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="qas_enabled" value="1"
                         {{ old('qas_enabled', $row->qas_enabled ?? true) ? 'checked' : '' }}>
                  <span class="form-check-label fw-bold">QAS Environment</span>
                </label>
              </h4>
            </div>
            <div class="card-body">
              <div class="row mb-3">
                <label class="col-lg-3 col-form-label fw-bold">Database</label>
                <div class="col-lg-9">
                  <input type="text" name="qas_db" class="form-control form-control-solid"
                         value="{{ old('qas_db', $row->qas_db ?? 'sap_qas') }}">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-lg-3 col-form-label required fw-bold">Table</label>
                <div class="col-lg-9">
                  <input type="text" name="qas_table" class="form-control form-control-solid"
                         value="{{ old('qas_table', $row->qas_table ?? '') }}" required>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-lg-3 col-form-label fw-bold">Client</label>
                <div class="col-lg-9">
                  <input type="text" name="qas_client" class="form-control form-control-solid"
                         value="{{ old('qas_client', $row->qas_client ?? '300') }}">
                </div>
              </div>
              
              @if(isset($data['id']) && $row)
              <hr class="my-4">
              <div class="row mb-3">
                <label class="col-lg-3 col-form-label fw-bold">Last Status</label>
                <div class="col-lg-9">
                  @if($row->qas_last_status)
                    @if($row->qas_last_status === 'success')
                      <span class="badge bg-success fs-6">{{ $row->qas_last_status }}</span>
                    @elseif($row->qas_last_status === 'failed')
                      <span class="badge bg-danger fs-6">{{ $row->qas_last_status }}</span>
                    @else
                      <span class="badge bg-secondary fs-6">{{ $row->qas_last_status }}</span>
                    @endif
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </div>
              </div>
              
              @if($row->qas_last_error)
              <div class="row mb-3">
                <label class="col-lg-3 col-form-label fw-bold text-danger">Last Error</label>
                <div class="col-lg-9">
                  <div class="alert alert-danger p-3 mb-0">
                    <small>{{ $row->qas_last_error }}</small>
                  </div>
                </div>
              </div>
              @endif

              @if($row->qas_last_run)
              <div class="row mb-3">
                <label class="col-lg-3 col-form-label fw-bold">Last Run</label>
                <div class="col-lg-9">
                  <span class="text-muted">{{ $row->qas_last_run->format('Y-m-d H:i:s') }}</span>
                </div>
              </div>
              @endif

              @if($row->qas_next_run)
              <div class="row">
                <label class="col-lg-3 col-form-label fw-bold">Next Run</label>
                <div class="col-lg-9">
                  <span class="text-muted">{{ $row->qas_next_run->format('Y-m-d H:i:s') }}</span>
                </div>
              </div>
              @endif
              @endif
            </div>
          </div>

          {{-- PROD Environment --}}
          <div class="card mb-5">
            <div class="card-header">
              <h4 class="card-title">
                <label class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="prod_enabled" value="1"
                         {{ old('prod_enabled', $row->prod_enabled ?? true) ? 'checked' : '' }}>
                  <span class="form-check-label fw-bold">PROD Environment</span>
                </label>
              </h4>
            </div>
            <div class="card-body">
              <div class="row mb-3">
                <label class="col-lg-3 col-form-label fw-bold">Database</label>
                <div class="col-lg-9">
                  <input type="text" name="prod_db" class="form-control form-control-solid"
                         value="{{ old('prod_db', $row->prod_db ?? 'sap_prod') }}">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-lg-3 col-form-label required fw-bold">Table</label>
                <div class="col-lg-9">
                  <input type="text" name="prod_table" class="form-control form-control-solid"
                         value="{{ old('prod_table', $row->prod_table ?? '') }}" required>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-lg-3 col-form-label fw-bold">Client</label>
                <div class="col-lg-9">
                  <input type="text" name="prod_client" class="form-control form-control-solid"
                         value="{{ old('prod_client', $row->prod_client ?? '300') }}">
                </div>
              </div>
              
              @if(isset($data['id']) && $row)
              <hr class="my-4">
              <div class="row mb-3">
                <label class="col-lg-3 col-form-label fw-bold">Last Status</label>
                <div class="col-lg-9">
                  @if($row->prod_last_status)
                    @if($row->prod_last_status === 'success')
                      <span class="badge bg-success fs-6">{{ $row->prod_last_status }}</span>
                    @elseif($row->prod_last_status === 'failed')
                      <span class="badge bg-danger fs-6">{{ $row->prod_last_status }}</span>
                    @else
                      <span class="badge bg-secondary fs-6">{{ $row->prod_last_status }}</span>
                    @endif
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </div>
              </div>
              
              @if($row->prod_last_error)
              <div class="row mb-3">
                <label class="col-lg-3 col-form-label fw-bold text-danger">Last Error</label>
                <div class="col-lg-9">
                  <div class="alert alert-danger p-3 mb-0">
                    <small>{{ $row->prod_last_error }}</small>
                  </div>
                </div>
              </div>
              @endif

              @if($row->prod_last_run)
              <div class="row mb-3">
                <label class="col-lg-3 col-form-label fw-bold">Last Run</label>
                <div class="col-lg-9">
                  <span class="text-muted">{{ $row->prod_last_run->format('Y-m-d H:i:s') }}</span>
                </div>
              </div>
              @endif

              @if($row->prod_next_run)
              <div class="row">
                <label class="col-lg-3 col-form-label fw-bold">Next Run</label>
                <div class="col-lg-9">
                  <span class="text-muted">{{ $row->prod_next_run->format('Y-m-d H:i:s') }}</span>
                </div>
              </div>
              @endif
              @endif
            </div>
          </div>

          <hr class="my-8">

          {{-- Connection IDs --}}
          <div class="row mb-5">
            <label class="col-lg-3 col-form-label fw-bold fs-6">ClickHouse Connection ID</label>
            <div class="col-lg-9">
              <input type="text" name="ck_conn_id" class="form-control form-control-solid"
                     value="{{ old('ck_conn_id', $row->ck_conn_id ?? 'clickhouse_mitratel') }}">
            </div>
          </div>

          <div class="row mb-5">
            <label class="col-lg-3 col-form-label fw-bold fs-6">Log Connection ID</label>
            <div class="col-lg-9">
              <input type="text" name="log_conn_id" class="form-control form-control-solid"
                     value="{{ old('log_conn_id', $row->log_conn_id ?? 'airflow_logs_mitratel') }}">
            </div>
          </div>

        </div>

        <div class="card-footer d-flex justify-content-between py-6 px-9">
          <div>
            @if(isset($data['id']) && $row)
              <button type="button" class="btn btn-warning" onclick="document.getElementById('forceInitialForm').submit();">
                <i class="bi bi-arrow-clockwise"></i> Force Initial Load
              </button>
            @endif
          </div>
          <div>
            <a href="{{ route('manage_sap_cdc') }}" class="btn btn-light btn-active-light-primary me-2">Cancel</a>
            <button type="submit" class="btn btn-danger">
              {{ isset($data['id']) ? 'Update' : 'Create' }}
            </button>
          </div>
        </div>
      </form>

      {{-- Separate Force Initial Form --}}
      @if(isset($data['id']) && $row)
      <form id="forceInitialForm" action="{{ route('force_initial_sap_cdc', $data['id']) }}" method="POST" style="display:none;" onsubmit="return confirm('This will force a full initial load for all enabled environments. Are you sure?');">
        @csrf 
        @method('PATCH')
      </form>
      @endif
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const scheduleType = document.getElementById('schedule_type');
  const intervalRow = document.getElementById('interval_row');
  const cronRow = document.getElementById('cron_row');

  function toggleScheduleFields() {
    if (scheduleType.value === 'interval') {
      intervalRow.style.display = '';
      cronRow.style.display = 'none';
    } else {
      intervalRow.style.display = 'none';
      cronRow.style.display = '';
    }
  }

  scheduleType.addEventListener('change', toggleScheduleFields);
  toggleScheduleFields();
});
</script>
@endsection