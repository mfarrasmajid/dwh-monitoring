@extends('layouts.blank')

@section('title', 'Portal')

@section('content')
<div class="row g-5 mx-5">
    @if (session('error'))
    <div class="col-lg-12">
        <div class="alert alert-danger d-flex align-items-center p-5 mt-5 mb-0 mx-10">
            <span class="svg-icon svg-icon-2hx svg-icon-danger me-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"/>
                <rect x="9" y="13.0283" width="7.3536" height="1.2256" rx="0.6128" transform="rotate(-45 9 13.0283)" fill="currentColor"/>
                <rect x="9.86664" y="7.93359" width="7.3536" height="1.2256" rx="0.6128" transform="rotate(45 9.86664 7.93359)" fill="currentColor"/>
                </svg>
            </span>
            <div class="d-flex flex-column">
                <h6 class="mb-1 text-danger">@php echo session('error'); @endphp</h6>
            </div>
        </div>
    </div>
    @endif
    <div class="col-lg-3 d-lg-none">
        <div class="d-flex align-items-stretch flex-column">
            <div class="d-flex w-100 my-10 flex-row-fluid justify-content-center">
                <img class="h-100px" src="{{ asset('assets/_dwh/logo.png')}}" alt="Logo Mitratel">
            </div>
            <div class="d-flex card my-5 d-lg-none mx-5">
                <div class="card-body p-5">
                    <div class="d-flex flex-row align-items-center">
                        <div class="ms-5 d-flex flex-column">
                            <div class="fs-1 fw-bolder">{{ session()->get('user')->name }}</div>
                            <div class="fs-6 text-muted">{{ session()->get('user')->nik_tg }}</div>
                        </div>
                    </div>
                    <!--end::Photo-->
                </div>
            </div>
            <div class="d-flex flex-column align-items-between">
                <div class="card bg-hover-danger bg-opacity-75 bg-white border-0 mb-5 ms-5 me-5 me-lg-0" >
                    <div class="card-body p-0 text-center">
                        <a href="{{ url('/dwh/dashboard')}}">
                            <div class="d-flex p-5 flex-column-fluid justify-content-center">
                                <img src="{{ asset('assets/_dwh/logo-airflow.png')}}" alt="Logo AirFlow" class="h-50px">
                            </div>
                        </a>
                    </div>
                </div>
                <div class="d-flex px-5 d-lg-none">
                    <a href="{{url('/logout')}}" class="btn btn-danger w-100 mb-10">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 d-lg-block d-none">
        <div class="row g-5">
            <div class="col-lg-6">
                <div class="d-flex w-100 my-10 flex-row-fluid justify-content-center">
                    <img class="h-100px" src="{{ asset('assets/_dwh/logo.png')}}" alt="Logo Mitratel">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-flush h-50 mt-5" style="min-height:200px !important;">
                    <!--begin::Header-->
                    <div class="card-header align-items-center py-6">
                        <!--begin::Info-->
                        <div class="card-title flex-column flex-grow-1">
                            <span class="card-label fw-bold fs-1">{{ session()->get('user')->name }}</span>
                            <span class="opacity-50 fw-semibold fs-6">{{ session()->get('user')->nik_tg }}</span>
                        </div>
                        <!--end::Info-->
                        <a href="{{url('/logout')}}" class="btn btn-danger w-100 mb-10">Logout</a>
                    </div>
                    <!--end::Header-->
                </div>
            </div>
        </div>    
        <div class="row g-5 mt-5 d-lg-flex d-none">
            @if ((session()->get('user')->privilege == 'ADMIN') || (session()->get('user')->privilege == 'USER'))
            <div class="col-lg-4">
                <div class="card card-flush h-100">
                    <div class="card-body px-15 pb-15 pt-10">
                        <div class="fs-2x fw-bolder text-dark text-hover-danger mb-5">Datawarehouse</div>
                        <img src="{{ asset('assets/_dwh/logo.png')}}" alt="Logo Mitratel" class="w-100 mb-10 d-block">
                        <a href="{{ url('/dwh/dashboard')}}" class="btn btn-light-danger btn-sm w-100">Dashboard DWH</a>
                    </div>
                </div>
            </div>
            @endif
            @if (session()->get('user')->privilege == 'ADMIN')
            <div class="col-lg-4">
                <div class="card card-flush h-100">
                    <div class="card-body px-15 pb-15 pt-10">
                        <div class="fs-2x fw-bolder text-dark text-hover-danger mb-5">Admin</div>
                        <img src="{{ asset('assets/_dwh/logo.png')}}" alt="Logo Admin" class="w-100 mb-10 d-block">
                        <a href="{{ url('/admin/dashboard_admin')}}" class="btn btn-light-danger btn-sm w-100">Dashboard Admin</a>
                    </div>
                </div>
            </div>
            @endif
            {{-- <div class="col-lg-4">
                <div class="card card-flush h-100">
                    <div class="card-body px-15 pb-15 pt-10">
                        <div class="fs-2x fw-bolder text-dark text-hover-danger mb-5">Helpdesk</div>
                        <img src="{{ asset('assets/_dwh/logo.png')}}" alt="Logo Admin" class="w-100 mb-10 d-block">
                        <a href="{{ url('/helpdesk/dashboard')}}" class="btn btn-light-danger btn-sm w-100">Dashboard Helpdesk</a>
                    </div>
                </div>
            </div> --}}
        </div>  
    </div>
</div>
@endsection

@section('styles')
<style>
    .hidden {display:none !important;}
</style>
@stop 

@section('scripts')
<script>
</script>
@stop 
