@extends('layouts.main')

@section('title', 'Dashboard Helpdesk')

@section('toolbar')
<div class="app-header-wrapper d-flex align-items-center justify-content-around justify-content-lg-between flex-wrap gap-6 gap-lg-0 mb-6 mb-lg-0" data-kt-swapper="true" data-kt-swapper-mode="{default: 'prepend', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_content_container', lg: '#kt_app_header_wrapper'}">
    <!--begin::Page title-->
    <div class="d-flex flex-column justify-content-center">
        <!--begin::Title-->
        <h1 class="text-gray-900 fw-bold fs-2x mb-2">Dashboard Helpdesk</h1>
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
                <a href="{{ url('helpdesk/dashboard') }}" class="text-muted text-hover-danger">Dashboard Helpdesk</a>
            </li>
            <!--end::Item-->
            <!--begin::Item-->
            <li class="breadcrumb-item text-muted">/</li>
            <!--end::Item-->
            <!--begin::Item-->
            <li class="breadcrumb-item text-muted">Trouble Ticket</li>
            <!--end::Item-->
        </ul>
        <!--end::Breadcrumb-->
    </div>
    <!--end::Page title-->
    {{-- <div class="d-none d-md-block h-40px border-start border-gray-200 mx-10"></div> --}}
    <div class="d-flex gap-3 gap-lg-8 flex-wrap">
    </div>
</div>
@endsection

@section('content')
<!--begin::Row-->
<div class="row g-5">
    @if (session('success'))
    <div class="col-xl-12">
        <div class="alert alert-success d-flex align-items-center p-5 mb-10">
            <span class="svg-icon svg-icon-2hx svg-icon-success me-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"/>
                <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor"/>
                </svg>
            </span>
            <div class="d-flex flex-column">
                <h6 class="mb-1 text-success">{{ session('success') }}</h6>
            </div>
        </div>
    </div>
    @endif
    @if (session('error'))
    <div class="col-xl-12">
        <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
            <span class="svg-icon svg-icon-2hx svg-icon-danger me-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"/>
                <rect x="9" y="13.0283" width="7.3536" height="1.2256" rx="0.6128" transform="rotate(-45 9 13.0283)" fill="currentColor"/>
                <rect x="9.86664" y="7.93359" width="7.3536" height="1.2256" rx="0.6128" transform="rotate(45 9.86664 7.93359)" fill="currentColor"/>
                </svg>
            </span>
            <div class="d-flex flex-column">
                <h6 class="mb-1 text-danger">{{ session('error') }}</h6>
            </div>
        </div>
    </div>
    @endif
    <!--begin::Col-->
    <div class="col-xl-12">
        <div class="card card-flush mb-5">
            <!--begin::Header-->
            <div class="card-header align-items-center pt-6 pb-6" style="background: linear-gradient(180deg, rgb(204, 14, 14) 0%, rgb(201, 57, 57) 100%);">
                <!--begin::Symbol-->
                <div class="symbol symbol-50px me-4">
                    <div class="symbol-label bg-transparent rounded-lg" style="border: 1px dashed rgba(255, 255, 255, 0.20)">
                        <i class="ki-outline ki-messages text-white fs-1"></i>
                    </div>
                </div>
                <!--end::Symbol-->
                <!--begin::Info-->
                <div class="card-title flex-column flex-grow-1">
                    <span class="card-label fw-bold fs-1 text-white">Selamat Pagi, {{Session::get('user')->name}}!<br>Data Trouble Ticket terakhir diupdate pada: {{$data['latest_modified']}}</span>
                    <span class="text-white opacity-50 fw-semibold fs-4">Cek list trouble ticket disini:</span>
                </div>
                <!--end::Info-->
            </div>
            <!--end::Header-->
            <!--begin::Card body-->
            <div class="card-body d-flex flex-column align-items-start">
                <button class="btn btn-light-danger btn-sm mb-3" onclick="exportFilteredData()">Export to CSV</button>
                <p id="rowCount"></p>
                <!--begin::Wrapper-->
                <div id="myGrid"></div>
                <!--end::Wrapper-->
            </div>
            <!--end::Card body-->
        </div>
    </div>
</div>
@stop

@section('styles')
<style>
    #myGrid {
        height: 600px;
        width: 100%;
    }
    .ag-root {
        user-select: text;
        -webkit-user-select: text;
        -moz-user-select: text;
        -ms-user-select: text;
    }
</style>
@stop
  
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js"></script>
<script>
    const columnDefs = [
        { field: 'status', headerName: 'Status', sortable: true, filter: true, minWidth: 200 },
        { field: 'name', headerName: 'Name', sortable: true, filter: true, minWidth: 200 },
        { field: 'docstatus', headerName: 'Docstatus', sortable: true, filter: true, minWidth: 200 },
        { field: 'aging', headerName: 'Aging', sortable: true, filter: true, minWidth: 200 },
        { field: 'priority', headerName: 'Severity', sortable: true, filter: true, minWidth: 200 },
        { field: 'pid', headerName: 'PID', sortable: true, filter: true, minWidth: 200 },
        { field: 'tower_owner', headerName: 'Tower Owner', sortable: true, filter: true, minWidth: 200 },
        { field: 'portofolio', headerName: 'Portofolio', sortable: true, filter: true, minWidth: 200 },
        { field: 'region', headerName: 'Region', sortable: true, filter: true, minWidth: 200 },
        { field: 'issue_type', headerName: 'Category', sortable: true, filter: true, minWidth: 200 },
        { field: 'tt_description', headerName: 'TT Description', sortable: true, filter: true, minWidth: 200 },
        { field: 'area', headerName: 'Area', sortable: true, filter: true, minWidth: 200 },
        { field: 'address', headerName: 'Address', sortable: true, filter: true, minWidth: 200 },
        { field: 'mitra_om', headerName: 'Vendor Mitra OM', sortable: true, filter: true, minWidth: 200 },
        {
            field: 'ts_closed',
            headerName: 'Timestamp Closed',
            filter: 'agDateColumnFilter',
            sortable: true,
            minWidth: 250,
            filterParams: {
                browserDatePicker: true,
                comparator: function (filterLocalDateAtMidnight, cellValue) {
                    if (!cellValue) return -1;
                    const cellDate = new Date(cellValue);
                    const cellDateOnly = new Date(cellDate.getFullYear(), cellDate.getMonth(), cellDate.getDate());
                    if (cellDateOnly < filterLocalDateAtMidnight) return -1;
                    if (cellDateOnly > filterLocalDateAtMidnight) return 1;
                    return 0;
                }
            },
            valueFormatter: (params) => {
                if (!params.value) return '';
                const d = new Date(params.value);
                return d.toLocaleDateString('en-GB') + ' ' + d.toLocaleTimeString('en-GB');
            }
        },
        { field: 'customer', headerName: 'Customer', sortable: true, filter: true, minWidth: 200 },
        { field: 'tower_name', headerName: 'Tower Name', sortable: true, filter: true, minWidth: 200 },
        { field: 'pic', headerName: 'PIC', sortable: true, filter: true, minWidth: 200 },
        {
            field: 'ts_open',
            headerName: 'Timestamp Open',
            filter: 'agDateColumnFilter',
            minWidth: 250,
            sortable: true,
            filterParams: {
                browserDatePicker: true,
                comparator: function (filterLocalDateAtMidnight, cellValue) {
                    if (!cellValue) return -1;
                    const cellDate = new Date(cellValue);
                    const cellDateOnly = new Date(cellDate.getFullYear(), cellDate.getMonth(), cellDate.getDate());
                    if (cellDateOnly < filterLocalDateAtMidnight) return -1;
                    if (cellDateOnly > filterLocalDateAtMidnight) return 1;
                    return 0;
                }
            },
            valueFormatter: (params) => {
                if (!params.value) return '';
                const d = new Date(params.value);
                return d.toLocaleDateString('en-GB') + ' ' + d.toLocaleTimeString('en-GB');
            }
        },
        { field: 'tt_ant_id', headerName: 'TT ID Tenant', sortable: true, filter: true, minWidth: 200 },
        { field: 'tenant_id', headerName: 'Tenant ID', sortable: true, filter: true, minWidth: 200 },
        { field: 'sid_tenant', headerName: 'SiteID from Tenant', sortable: true, filter: true, minWidth: 200 },
        {
            field: 'tanggal_request_ant',
            headerName: 'Tanggal Request ANT',
            filter: 'agDateColumnFilter',
            minWidth: 250,
            sortable: true,
            filterParams: {
                browserDatePicker: true,
                comparator: function (filterLocalDateAtMidnight, cellValue) {
                    if (!cellValue) return -1;
                    const cellDate = new Date(cellValue);
                    const cellDateOnly = new Date(cellDate.getFullYear(), cellDate.getMonth(), cellDate.getDate());
                    if (cellDateOnly < filterLocalDateAtMidnight) return -1;
                    if (cellDateOnly > filterLocalDateAtMidnight) return 1;
                    return 0;
                }
            },
            valueFormatter: (params) => {
                if (!params.value) return '';
                const d = new Date(params.value);
                return d.toLocaleDateString('en-GB') + ' ' + d.toLocaleTimeString('en-GB');
            }
        },
        {
            field: 'ts_resolved',
            headerName: 'Timestamp Resolved',
            filter: 'agDateColumnFilter',
            minWidth: 250,
            sortable: true,
            filterParams: {
                browserDatePicker: true,
                comparator: function (filterLocalDateAtMidnight, cellValue) {
                    if (!cellValue) return -1;
                    const cellDate = new Date(cellValue);
                    const cellDateOnly = new Date(cellDate.getFullYear(), cellDate.getMonth(), cellDate.getDate());
                    if (cellDateOnly < filterLocalDateAtMidnight) return -1;
                    if (cellDateOnly > filterLocalDateAtMidnight) return 1;
                    return 0;
                }
            },
            valueFormatter: (params) => {
                if (!params.value) return '';
                const d = new Date(params.value);
                return d.toLocaleDateString('en-GB') + ' ' + d.toLocaleTimeString('en-GB');
            }
        },
        { field: 'service_level_agreement', headerName: 'Service Level Agreement', sortable: true, filter: true, minWidth: 200 },
        { field: 'agreement_status', headerName: 'Service Level Agreement Status', sortable: true, filter: true, minWidth: 200 },
        { field: 'actual_category', headerName: 'Actual Category', sortable: true, filter: true, minWidth: 200 },
        { field: 'reference', headerName: 'TT From', sortable: true, filter: true, minWidth: 200 },
        { field: 'tt_id_ampuhc', headerName: 'TT ID AmpuhC', sortable: true, filter: true, minWidth: 200 },
        { field: 'class_of_service', headerName: 'Class of Service', sortable: true, filter: true, minWidth: 200 },
        { field: 'maintenance_zone', headerName: 'Maintenance Zone', sortable: true, filter: true, minWidth: 200 },
        { field: 'engineer', headerName: 'Fullname Engineer', sortable: true, filter: true, minWidth: 200 },
        { field: 'sla_status', headerName: 'SLA Status', sortable: true, filter: true, minWidth: 200 },
        { field: 'mttr', headerName: 'MTTR', sortable: true, filter: true, minWidth: 200 },
        { field: 'mttr_hours', headerName: 'MTTR Hours', sortable: true, filter: true, minWidth: 200 },
    ];

    const gridOptions = {
      columnDefs: columnDefs,
      rowModelType: 'infinite',
      paginationPageSize: 50,
      cacheBlockSize: 50,
      datasource: {
        getRows: function (params) {
          const startRow = params.startRow;
          const endRow = params.endRow;

          const filterModel = JSON.stringify(params.filterModel);
          const sortModel = JSON.stringify(params.sortModel);

          const query = new URLSearchParams({
            startRow: startRow,
            endRow: endRow,
            filterModel: filterModel,
            sortModel: sortModel
          });

          fetch(`{{ url('/api/helpdesk/get_list_trouble_ticket') }}?${query}`, {
            headers: { 'Content-Type': 'application/json' }
          })
          .then(res => res.json())
          .then(data => {
            params.successCallback(data.rows, data.lastRow);
            document.getElementById('rowCount').textContent =
                `Showing ${data.totalFiltered} filtered rows out of ${data.totalAll} total rows`;
          })
          .catch(err => {
            console.error(err);
            params.failCallback();
          });
        }
      },
      defaultColDef: {
        flex: 1,
        minWidth: 100,
        resizable: true
      },
      onGridReady: (params) => {
        window.gridApi = params.api;
        window.gridColumnApi = params.columnApi;
        }
    };

    const gridDiv = document.getElementById('myGrid');
    const apiGrid = agGrid.createGrid(gridDiv, gridOptions);

    function exportFilteredData() {
        if (!window.gridApi) {
            alert("Grid is not ready yet!");
            return;
        }
        const filterModel = apiGrid.getFilterModel();

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ url('/api/helpdesk/export_trouble_ticket') }}";
        form.target = '_blank';

        const csrf = document.createElement('input');
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        const filterInput = document.createElement('input');
        filterInput.name = 'filterModel';
        filterInput.value = JSON.stringify(filterModel);
        form.appendChild(filterInput);

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
</script>
@stop
