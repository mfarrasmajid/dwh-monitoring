@extends('layouts.main')

@section('title', 'Manage Datawarehouse')

@section('toolbar')
<div class="app-header-wrapper d-flex align-items-center justify-content-around justify-content-lg-between flex-wrap gap-6 gap-lg-0 mb-6 mb-lg-0" data-kt-swapper="true" data-kt-swapper-mode="{default: 'prepend', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_content_container', lg: '#kt_app_header_wrapper'}">
    <div class="d-flex flex-column justify-content-center">
        <h1 class="text-gray-900 fw-bold fs-2x mb-2">Manage Datawarehouse</h1>
        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-base">
            <li class="breadcrumb-item text-muted">
                <a href="{{ url('/') }}" class="text-muted text-hover-danger">Portal</a>
            </li>
            <li class="breadcrumb-item text-muted">/</li>
            <li class="breadcrumb-item text-muted">
                <a href="{{ url('/admin/dashboard_admin') }}" class="text-muted text-hover-danger">Dashboard Admin</a>
            </li>
            <li class="breadcrumb-item text-muted">/</li>
            <li class="breadcrumb-item text-muted">Manage Datawarehouse</li>
        </ul>
    </div>
    <div class="d-flex gap-3 gap-lg-8 flex-wrap"></div>
</div>
@stop

@section('content')
<div class="card card-flush shadow-sm p-5">

  {{-- Top bar: global search + add --}}
  <div class="d-flex justify-content-between align-items-center mb-6">
    <form method="GET" class="d-flex gap-3">
      <input type="text" name="q" class="form-control form-control-solid"
             value="{{ request('q') }}" placeholder="Search db/table/conn id...">
      <button class="btn btn-light">Search</button>
      @if(request()->has('filter'))
        <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Reset</a>
      @endif
    </form>
    <a href="{{ route('create_datawarehouse') }}" class="btn btn-danger">New Job</a>
  </div>

  @if(session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif

  <div class="table-responsive">
    {{-- single form so filters persist with pagination --}}
    <form method="GET" id="filterForm">
      <input type="hidden" name="q" value="{{ request('q') }}">

      <table class="table align-middle">
        <thead>
          <tr>
            <th style="width:72px">ID</th>
            <th style="width:110px">Enabled</th>
            <th>Source</th>
            <th>Target</th>
            <th>PK / Version</th>
            <th>Schedule</th>
            <th>Parallel</th>
            <th>Last Status</th>
            <th class="text-end" style="width:180px"></th>
          </tr>

          {{-- filter row --}}
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
                     placeholder="src db/conn">
            </th>

            <th>
              <input class="form-control form-control-sm"
                     name="filter[target]"
                     value="{{ $f['target'] ?? '' }}"
                     placeholder="tgt db/table/conn">
            </th>

            <th>
              <div class="d-flex gap-2">
                <input class="form-control form-control-sm" name="filter[pk_cols]" value="{{ $f['pk_cols'] ?? '' }}" placeholder="pk cols">
                <input class="form-control form-control-sm" name="filter[version_col]" value="{{ $f['version_col'] ?? '' }}" placeholder="version col">
              </div>
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
              <div class="d-flex gap-2">
                <input type="number" min="1" class="form-control form-control-sm"
                       name="filter[parallel_slices]" value="{{ $f['parallel_slices'] ?? '' }}"
                       placeholder="slices">
                <input type="number" min="1" class="form-control form-control-sm"
                       name="filter[page_rows]" value="{{ $f['page_rows'] ?? '' }}"
                       placeholder="rows/page">
              </div>
            </th>

            <th>
              <input class="form-control form-control-sm"
                     name="filter[last_status]"
                     value="{{ $f['last_status'] ?? '' }}"
                     placeholder="Status">
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
                <form action="{{ route('toggle_datawarehouse', $r) }}" method="POST">
                  @csrf @method('PATCH')
                  <button class="btn btn-sm {{ $r->enabled ? 'btn-success' : 'btn-secondary' }}">
                    {{ $r->enabled ? 'Enabled' : 'Disabled' }}
                  </button>
                </form>
              </td>

              <td>
                <div><code>{{ $r->src_database }}</code></div>
                <small class="text-muted">{{ $r->source_ch_conn_id }}</small>
              </td>

              <td>
                <div><code>{{ $r->target_db }}.{{ $r->target_table }}</code></div>
                <small class="text-muted">{{ $r->target_ch_conn_id }}</small>
              </td>

              <td class="text-nowrap">
                <div>PK: <code>{{ $r->pk_cols }}</code></div>
                <div>Ver: <code>{{ $r->version_col ?? '-' }}</code></div>
              </td>

              <td class="text-nowrap">
                @if($r->schedule_type === 'cron')
                  <span class="badge bg-info">cron</span>
                  <code>{{ $r->cron_expr }}</code>
                @else
                  every {{ $r->interval_minutes ?? 15 }} min
                @endif
              </td>

              <td class="text-nowrap">{{ $r->parallel_slices }} × {{ number_format($r->page_rows) }}</td>

              <td>
                <div>
                  @if($r->last_status === 'success')
                    <span class="badge bg-success">success</span>
                  @elseif($r->last_status === 'failed')
                    <span class="badge bg-danger">failed</span>
                  @elseif($r->last_status)
                    <span class="badge bg-secondary">{{ $r->last_status }}</span>
                  @else
                    <span class="badge bg-light text-muted">-</span>
                  @endif
                </div>
                <div class="small text-muted">
                  next: {{ optional($r->next_run_at)->format('Y-m-d H:i') ?? '-' }}
                </div>
              </td>

              <td class="text-end text-nowrap">
                <form action="{{ route('queue_datawarehouse', $r) }}" method="POST" class="d-inline">
                  @csrf @method('PATCH')
                  <button class="btn btn-sm btn-primary"
                          title="Queue this pipeline to run ASAP">
                    Run Now
                  </button>
                </form>
                <a href="{{ route('edit_datawarehouse', $r) }}" class="btn btn-sm btn-light">Edit</a>
                <form action="{{ route('destroy_datawarehouse', $r) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Delete job #{{ $r->id }}?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="9" class="text-center text-muted py-4">No jobs yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </form>
  </div>

  <div class="mt-4 navigation-custom">
    {{-- preserve filters & search on pagination --}}
    {{ $rows->appends(request()->query())->links() }}
  </div>
</div>
@endsection

@section('styles')
<style>
  .navigation-custom svg { width: 20px !important; height: 20px !important; }
</style>
@endsection

@section('scripts')
<script>
  // submit filters when hitting Enter on any input inside filterForm
  const ff = document.getElementById('filterForm');
  if (ff) {
    ff.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') e.currentTarget.submit();
    });
  }
</script>
@endsection
