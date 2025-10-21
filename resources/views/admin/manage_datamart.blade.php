@extends('layouts.main')

@section('title', 'Manage Datamart')

@section('toolbar')
<div class="app-header-wrapper d-flex align-items-center justify-content-around justify-content-lg-between flex-wrap gap-6 gap-lg-0 mb-6 mb-lg-0" data-kt-swapper="true" data-kt-swapper-mode="{default: 'prepend', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_content_container', lg: '#kt_app_header_wrapper'}">
    <div class="d-flex flex-column justify-content-center">
        <h1 class="text-gray-900 fw-bold fs-2x mb-2">Manage Datamart</h1>
        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-base">
            <li class="breadcrumb-item text-muted">
                <a href="{{ url('/') }}" class="text-muted text-hover-danger">Portal</a>
            </li>
            <li class="breadcrumb-item text-muted">/</li>
            <li class="breadcrumb-item text-muted">
                <a href="{{ url('/admin/dashboard_admin') }}" class="text-muted text-hover-danger">Dashboard Admin</a>
            </li>
            <li class="breadcrumb-item text-muted">/</li>
            <li class="breadcrumb-item text-muted">Manage Datamart</li>
        </ul>
    </div>
    <div class="d-flex gap-3 gap-lg-8 flex-wrap"></div>
</div>
@endsection

@section('content')
@php
    use Illuminate\Support\Str;
    $f = request('filter', []);
@endphp

<div class="card card-flush shadow-sm p-5">

  {{-- Top bar: global search + add --}}
  <div class="d-flex justify-content-between align-items-center mb-6">
    <form method="GET" class="d-flex gap-3 w-100" style="max-width: 900px;">
      <input type="text" name="q" class="form-control form-control-solid"
             value="{{ request('q') }}" placeholder="Search source SQL / target / connection / status...">
      <button class="btn btn-light">Search</button>
      @if(request('q') || request()->has('filter'))
        <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Reset</a>
      @endif
    </form>
    <a href="{{ route('create_datamart') }}" class="btn btn-danger">New Job</a>
  </div>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif

  <div class="table-responsive">
    {{-- single form so filters persist with pagination --}}
      <table class="table align-middle">
        <thead>
          <tr>
            <th style="width:72px">ID</th>
            <th style="width:110px">Enabled</th>
            <th>Source (CH / SQL)</th>
            <th>Target</th>
            <th>PK / Cursor</th>
            <th>Schedule</th>
            <th>Batch / Workers</th>
            <th>Last Status</th>
            <th class="text-end" style="width:220px"></th>
          </tr>

          <form method="GET" id="filterForm">
            <input type="hidden" name="q" value="{{ request('q') }}">
          {{-- filter row --}}
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
                <option value="1" @if (isset($f['enabled'])) @if ($f['enabled'] == '1') selected @endif @endif>Enabled</option>
                <option value="0" @if (isset($f['enabled'])) @if ($f['enabled'] == '0') selected @endif @endif>Disabled</option>
              </select>
            </th>

            <th>
              <div class="d-flex gap-2">
                <input class="form-control form-control-sm"
                       name="filter[source_ch_conn_id]" value="{{ $f['source_ch_conn_id'] ?? '' }}"
                       placeholder="CH conn id">
                <input class="form-control form-control-sm"
                       name="filter[source_sql_like]" value="{{ $f['source_sql_like'] ?? '' }}"
                       placeholder="SQL contains...">
              </div>
            </th>

            <th>
              <div class="d-flex gap-2">
                <input class="form-control form-control-sm"
                       name="filter[target_schema]" value="{{ $f['target_schema'] ?? '' }}"
                       placeholder="schema">
                <input class="form-control form-control-sm"
                       name="filter[target_table]" value="{{ $f['target_table'] ?? '' }}"
                       placeholder="table">
                <input class="form-control form-control-sm"
                       name="filter[target_pg_conn_id]" value="{{ $f['target_pg_conn_id'] ?? '' }}"
                       placeholder="PG conn">
              </div>
            </th>

            <th>
              <div class="d-flex gap-2">
                <input class="form-control form-control-sm"
                       name="filter[pk_value]" value="{{ $f['pk_value'] ?? '' }}"
                       placeholder="pk e.g. id,code">
                <input class="form-control form-control-sm"
                       name="filter[cursor_col]"
                       value="{{ $f['cursor_col'] ?? '' }}"
                       placeholder="cursor col">
              </div>
            </th>

            <th>
              <div class="d-flex gap-2">
                <select class="form-select form-select-sm" name="filter[schedule_type]" style="max-width: 120px">
                  <option value="">Any</option>
                  <option value="interval" @if (isset($f['schedule_type'])) @if ($f['schedule_type'] == 'interval') selected @endif @endif>Interval</option>
                  <option value="cron" @if (isset($f['schedule_type'])) @if ($f['schedule_type'] == 'cron') selected @endif @endif>Cron</option>
                </select>
                <input class="form-control form-control-sm"
                       name="filter[interval_minutes]"
                       value="{{ $f['interval_minutes'] ?? '' }}"
                       placeholder="minutes / cron">
                <input class="form-control form-control-sm"
                       name="filter[cron_expr]"
                       value="{{ $f['cron_expr'] ?? '' }}"
                       placeholder="cron expr">
              </div>
            </th>

            <th>
              <div class="d-flex gap-2">
                <input type="number" min="1" class="form-control form-control-sm"
                       name="filter[chunk_rows]" value="{{ $f['chunk_rows'] ?? '' }}"
                       placeholder="chunk">
                <input type="number" min="1" class="form-control form-control-sm"
                       name="filter[max_parallel]" value="{{ $f['max_parallel'] ?? '' }}"
                       placeholder="workers">
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
          </form>
        </thead>

        <tbody>
          @forelse($rows as $r)
            <tr>
              <td>{{ $r->id }}</td>

              <td>
                <form action="{{ route('toggle_datamart', $r->id) }}" method="POST">
                  @csrf @method('PATCH')
                  <button class="btn btn-sm {{ $r->enabled ? 'btn-success' : 'btn-secondary' }}">
                    {{ $r->enabled ? 'Enabled' : 'Disabled' }}
                  </button>
                </form>
              </td>

              <td>
                <div class="mb-1"><small class="text-muted">CH:</small> <code>{{ $r->source_ch_conn_id }}</code></div>
                <details>
                  <summary class="small text-muted">SQL preview</summary>
                  <pre class="small bg-light rounded p-2 mb-0" style="max-width: 560px; white-space: pre-wrap;">{{ Str::limit($r->source_sql ?? '', 800) }}</pre>
                </details>
              </td>

              <td>
                <div><code>{{ $r->target_schema }}.{{ $r->target_table }}</code></div>
                <small class="text-muted">{{ $r->target_pg_conn_id }}</small>
              </td>

              <td class="text-nowrap">
                <div><small class="text-muted">PK:</small> <code>{{ $r->pk_value ?: '-' }}</code></div>
                <div><small class="text-muted">Cursor:</small> <code>{{ $r->cursor_col ?: '-' }}</code></div>
              </td>

              <td class="text-nowrap">
                @if($r->schedule_type === 'cron')
                  <span class="badge bg-info">cron</span>
                  <code>{{ $r->cron_expr }}</code>
                @else
                  every {{ $r->interval_minutes ?? 30 }} min
                @endif
              </td>

              <td class="text-nowrap">
                {{ number_format($r->chunk_rows ?? 0) }} / {{ $r->max_parallel ?? 1 }}
              </td>

              <td>
                <div>
                  @if($r->last_status === 'success')
                    <span class="badge bg-success">success</span>
                  @elseif($r->last_status === 'failed')
                    <span class="badge bg-danger" title="{{ $r->last_error }}">failed</span>
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
                <form action="{{ route('queue_datamart', $r->id) }}" method="POST" class="d-inline">
                  @csrf @method('PATCH')
                  <button class="btn btn-sm btn-primary" title="Queue to run ASAP">Run Now</button>
                </form>

                <a href="{{ route('edit_datamart', $r->id) }}" class="btn btn-sm btn-light">Edit</a>

                <form action="{{ route('destroy_datamart', $r->id) }}" method="POST" class="d-inline"
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
