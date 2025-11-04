@extends('layouts.main')

@section('title','Manage SAP CDC')
@section('toolbar')
<div class="app-header-wrapper d-flex align-items-center justify-content-around justify-content-lg-between flex-wrap gap-6 gap-lg-0 mb-6 mb-lg-0" data-kt-swapper="true" data-kt-swapper-mode="{default: 'prepend', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_content_container', lg: '#kt_app_header_wrapper'}">
    <div class="d-flex flex-column justify-content-center">
        <h1 class="text-gray-900 fw-bold fs-2x mb-2">Manage SAP CDC</h1>
        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-base">
            <li class="breadcrumb-item text-muted">
                <a href="{{ url('/') }}" class="text-muted text-hover-danger">Portal</a>
            </li>
            <li class="breadcrumb-item text-muted">/</li>
            <li class="breadcrumb-item text-muted">
                <a href="{{ url('/admin/dashboard_admin') }}" class="text-muted text-hover-danger">Dashboard Admin</a>
            </li>
            <li class="breadcrumb-item text-muted">/</li>
            <li class="breadcrumb-item text-muted">Manage SAP CDC</li>
        </ul>
    </div>
    <div class="d-flex gap-3 gap-lg-8 flex-wrap">
    </div>
</div>
@stop 

@section('content')
<div class="card card-flush shadow-sm p-5">
  <div class="d-flex justify-content-between align-items-center mb-6">
    <form method="GET" class="d-flex gap-3">
      <input type="text" name="q" class="form-control form-control-solid"
       value="{{ request('q') }}" placeholder="Search job code/service/entity...">
      <button class="btn btn-light">Search</button>
      @if(request()->has('filter'))
        <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Reset</a>
      @endif
    </form>
    <a href="{{ url('/admin/manage_sap_cdc/create') }}" class="btn btn-danger">Add SAP CDC Job</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Enabled</th>
          <th>Job Code</th>
          <th>Service/Entity</th>
          <th>Method</th>
          <th>Schedule</th>
          <th>Environments</th>
          <th>Last Status</th>
          <th></th>
        </tr>
        
        <form method="GET" id="filterForm" action="{{ url()->current() }}">
          <input type="hidden" name="q" value="{{ request('q') }}">

          @php($f = request('filter', []))
          <tr>
            <th>
              <input class="form-control form-control-sm"
                     name="filter[id]"
                     value="{{ $f['id'] ?? '' }}"
                     placeholder="ID">
            </th>

            <th>
              <select class="form-select form-select-sm" name="filter[is_enabled]" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="1" @if (isset($f['is_enabled']) && $f['is_enabled'] == '1') selected @endif>Enabled</option>
                <option value="0" @if (isset($f['is_enabled']) && $f['is_enabled'] == '0') selected @endif>Disabled</option>
              </select>
            </th>

            <th>
              <input class="form-control form-control-sm"
                     name="filter[job_code]"
                     value="{{ $f['job_code'] ?? '' }}"
                     placeholder="Job Code">
            </th>

            <th>
              <input class="form-control form-control-sm"
                     name="filter[service_name]"
                     value="{{ $f['service_name'] ?? '' }}"
                     placeholder="Service">
            </th>

            <th>
              <select class="form-select form-select-sm" name="filter[method]" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="continuous_cdc" @if (isset($f['method']) && $f['method'] == 'continuous_cdc') selected @endif>CDC</option>
                <option value="weekly_refresh" @if (isset($f['method']) && $f['method'] == 'weekly_refresh') selected @endif>Weekly</option>
              </select>
            </th>

            <th>
              <select class="form-select form-select-sm" name="filter[schedule_type]" onchange="this.form.submit()">
                <option value="">Any</option>
                <option value="interval" @if (isset($f['schedule_type']) && $f['schedule_type'] == 'interval') selected @endif>Interval</option>
                <option value="cron" @if (isset($f['schedule_type']) && $f['schedule_type'] == 'cron') selected @endif>Cron</option>
              </select>
            </th>

            <th>
              <select class="form-select form-select-sm" name="filter[environment]" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="sim" @if (isset($f['environment']) && $f['environment'] == 'sim') selected @endif>SIM</option>
                <option value="qas" @if (isset($f['environment']) && $f['environment'] == 'qas') selected @endif>QAS</option>
                <option value="prod" @if (isset($f['environment']) && $f['environment'] == 'prod') selected @endif>PROD</option>
              </select>
            </th>

            <th colspan="2" class="text-end">
              <button class="btn btn-sm btn-light">Apply</button>
              <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </th>
          </tr>
        </form>
      </thead>

      <tbody>
        @forelse($rows as $r)
          <tr>
            <td>{{ $r->id }}</td>
            <td>
              <form action="{{ route('toggle_sap_cdc', $r->id) }}" method="POST">
                @csrf @method('PATCH')
                <button class="btn btn-sm {{ $r->is_enabled ? 'btn-success' : 'btn-secondary' }}">
                  {{ $r->is_enabled ? 'Enabled' : 'Disabled' }}
                </button>
              </form>
            </td>
            <td><code>{{ $r->job_code }}</code></td>
            <td>
              <div><strong>{{ $r->service_name }}</strong></div>
              <small class="text-muted">{{ $r->entity_name }}</small>
            </td>
            <td>
              @if($r->method === 'continuous_cdc')
                <span class="badge bg-primary">CDC</span>
              @else
                <span class="badge bg-info">Weekly</span>
              @endif
            </td>
            <td>
              @if($r->schedule_type==='interval')
                every {{ $r->interval_minutes ?? 5 }} min
              @else
                cron: <code>{{ $r->cron_expr }}</code>
              @endif
            </td>
            <td>
              @if($r->sim_enabled) <span class="badge bg-light text-dark">SIM</span> @endif
              @if($r->qas_enabled) <span class="badge bg-warning text-dark">QAS</span> @endif
              @if($r->prod_enabled) <span class="badge bg-danger">PROD</span> @endif
            </td>
            <td>
              <div>
                @if($r->prod_enabled && $r->prod_last_status)
                  <small>PROD: 
                    @if($r->prod_last_status === 'success')
                      <span class="badge bg-success">success</span>
                    @elseif($r->prod_last_status === 'failed')
                      <span class="badge bg-danger">failed</span>
                    @else
                      <span class="badge bg-secondary">{{ $r->prod_last_status }}</span>
                    @endif
                  </small>
                @endif
              </div>
            </td>
            <td class="text-end">
              <form action="{{ route('trigger_sap_cdc', $r->id) }}" method="POST" class="d-inline">
                @csrf @method('PATCH')
                <button class="btn btn-sm btn-primary" title="Trigger run now">
                  Run Now
                </button>
              </form>
              <a class="btn btn-sm btn-light" href="{{ route('edit_sap_cdc', $r->id) }}">Edit</a>
              <form action="{{ route('destroy_sap_cdc', $r->id) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Delete this SAP CDC job?');">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="9" class="text-center text-muted">No rows.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4 navigation-custom">
    {{ $rows->appends(request()->query())->links() }}
  </div>
</div>
@endsection

@section('styles')
<style>
  .navigation-custom svg {
    width: 20px !important;
    height: 20px !important;
  }
</style>
@endsection 

@section('scripts')
<script>
  document.getElementById('filterForm').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') e.currentTarget.submit();
  });
</script>
@endsection