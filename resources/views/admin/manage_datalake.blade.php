{{-- resources/views/admin/ingestion_registry_index.blade.php --}}
@extends('layouts.main')

@section('title','Ingestion Registry')
@section('toolbar')
<div class="app-header-wrapper d-flex align-items-center justify-content-around justify-content-lg-between flex-wrap gap-6 gap-lg-0 mb-6 mb-lg-0" data-kt-swapper="true" data-kt-swapper-mode="{default: 'prepend', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_content_container', lg: '#kt_app_header_wrapper'}">
    <!--begin::Page title-->
    <div class="d-flex flex-column justify-content-center">
        <!--begin::Title-->
        <h1 class="text-gray-900 fw-bold fs-2x mb-2">Manage Datalake</h1>
        <!--end::Title-->
        <!--begin::Breadcrumb-->
        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-base">
            <!--begin::Item-->
            <li class="breadcrumb-item text-muted">
                <a href="{{ url('/') }}" class="text-muted text-hover-danger">Portal</a>
            </li>
            <!--end::Item-->
            <!--begin::Item-->
            <li class="breadcrumb-item text-muted">/</li>
            <!--end::Item-->
            <!--begin::Item-->
            <li class="breadcrumb-item text-muted">
                <a href="{{ url('/admin/dashboard_admin') }}" class="text-muted text-hover-danger">Dashboard Admin</a>
            </li>
            <!--end::Item-->
            <!--begin::Item-->
            <li class="breadcrumb-item text-muted">/</li>
            <!--end::Item-->
            <!--begin::Item-->
            <li class="breadcrumb-item text-muted">Manage Datalake</li>
            <!--end::Item-->
        </ul>
        <!--end::Breadcrumb-->
    </div>
    <!--end::Page title-->
    {{-- <div class="d-none d-md-block h-40px border-start border-gray-200 mx-10"></div> --}}
    <div class="d-flex gap-3 gap-lg-8 flex-wrap">
    </div>
</div>

<!--begin::Actions-->

<!--end::Actions-->
@stop 

@section('content')
<div class="card card-flush shadow-sm p-5">
  <div class="d-flex justify-content-between align-items-center mb-6">
    {{-- Global search stays --}}
    <form method="GET" class="d-flex gap-3">
      <input type="text" name="q" class="form-control form-control-solid"
       value="{{ request('q') }}" placeholder="Search db/table/conn id...">
      <button class="btn btn-light">Search</button>
      @if(request()->has('filter'))
        <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Reset</a>
      @endif
    </form>
    <a href="{{ url('/admin/ingestion_registry/create') }}" class="btn btn-danger">Add Ingestion</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="table-responsive">
    {{-- One form to rule them all (so paging preserves filters) --}}
    <form method="GET" id="filterForm">
      {{-- Keep global q when filtering per column --}}
      <input type="hidden" name="q" value="{{ request('q') }}">

      <table class="table align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Enabled</th>
            <th>Source</th>
            <th>Target</th>
            <th>Schedule</th>
            <th>Last Status</th>
            <th>Next Run</th>
            <th></th>
          </tr>

          {{-- FILTER ROW --}}
          @php($f = request('filter', []))
          <tr>
            <th>
              <input class="form-control form-control-sm"
                     name="filter[id]"
                     value="{{ $f['id'] ?? '' }}"
                     placeholder="ID">
            </th>

            <th>
              <select class="form-select form-select-sm" name="filter[enabled]" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="1" @selected(($f['enabled'] ?? '')=='1')>Enabled</option>
                <option value="0" @selected(($f['enabled'] ?? '')=='0')>Disabled</option>
              </select>
            </th>

            <th>
              <input class="form-control form-control-sm"
                     name="filter[source]"
                     value="{{ $f['source'] ?? '' }}"
                     placeholder="db/table/conn">
            </th>

            <th>
              <input class="form-control form-control-sm"
                     name="filter[target]"
                     value="{{ $f['target'] ?? '' }}"
                     placeholder="db/table/conn">
            </th>

            <th>
              <div class="d-flex gap-2">
                <select class="form-select form-select-sm" name="filter[schedule_type]" style="max-width: 120px">
                  <option value="">Any</option>
                  <option value="interval" @selected(($f['schedule_type'] ?? '')==='interval')>Interval</option>
                  <option value="cron" @selected(($f['schedule_type'] ?? '')==='cron')>Cron</option>
                </select>
                <input class="form-control form-control-sm"
                       name="filter[schedule_text]"
                       value="{{ $f['schedule_text'] ?? '' }}"
                       placeholder="min/cron expr">
              </div>
            </th>

            <th>
              <input class="form-control form-control-sm"
                     name="filter[last_status]"
                     value="{{ $f['last_status'] ?? '' }}"
                     placeholder="Status">
            </th>

            <th>
              <div class="d-flex gap-2">
                <input type="date" class="form-control form-control-sm" name="filter[next_run_from]" value="{{ $f['next_run_from'] ?? '' }}">
                <input type="date" class="form-control form-control-sm" name="filter[next_run_to]" value="{{ $f['next_run_to'] ?? '' }}">
              </div>
            </th>

            <th class="text-end">
              <button class="btn btn-sm btn-light">Apply</button>
              <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </th>
          </tr>
        </thead>

        <tbody>
          @forelse($rows as $r)
            <tr>
              <td>{{ $r->id }}</td>
              <td>
                <form action="{{ url('/admin/ingestion_registry/'.$r->id.'/toggle') }}" method="POST">
                  @csrf @method('PATCH')
                  <button class="btn btn-sm {{ $r->enabled ? 'btn-success' : 'btn-secondary' }}">
                    {{ $r->enabled ? 'Enabled' : 'Disabled' }}
                  </button>
                </form>
              </td>
              <td><code>{{ $r->source_db }}.{{ $r->source_table }}</code><br><small>{{ $r->source_mysql_conn_id }}</small></td>
              <td><code>{{ $r->target_db }}.{{ $r->target_table }}</code><br><small>{{ $r->target_ch_conn_id }}</small></td>
              <td>
                @if($r->schedule_type==='interval')
                  every {{ $r->interval_minutes ?? 15 }} min
                @else
                  cron: <code>{{ $r->cron_expr }}</code>
                @endif
              </td>
              <td>{{ $r->last_status ?? '-' }}</td>
              <td>{{ $r->next_run_at ?? '-' }}</td>
              <td class="text-end">
                <a class="btn btn-sm btn-light" href="{{ url('/admin/ingestion_registry/'.$r->id.'/edit') }}">Edit</a>
                <form action="{{ url('/admin/ingestion_registry/'.$r->id) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Delete this ingestion?');">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center text-muted">No rows.</td></tr>
          @endforelse
        </tbody>
      </table>
    </form>
  </div>

  <div class="mt-4 navigation-custom">
    {{-- keep filters on pagination --}}
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
