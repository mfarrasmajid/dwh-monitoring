@extends('layouts.main')

@section('title', 'Dashboard Portal Helpdesk')

@section('toolbar')
<div class="app-header-wrapper d-flex align-items-center justify-content-around justify-content-lg-between flex-wrap gap-6 gap-lg-0 mb-6 mb-lg-0" data-kt-swapper="true" data-kt-swapper-mode="{default: 'prepend', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_content_container', lg: '#kt_app_header_wrapper'}">
    <!--begin::Page title-->
    <div class="d-flex flex-column justify-content-center">
        <!--begin::Title-->
        <h1 class="text-gray-900 fw-bold fs-2x mb-2">Dashboard Portal Helpdesk</h1>
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
            <li class="breadcrumb-item text-muted">Dashboard Portal Helpdesk</li>
            <!--end::Item-->
        </ul>
        <!--end::Breadcrumb-->
    </div>
    <!--end::Page title-->
    {{-- <div class="d-none d-md-block h-40px border-start border-gray-200 mx-10"></div> --}}
    <div class="d-flex gap-3 gap-lg-8 flex-wrap">
    </div>
</div>
@stop

@section('content')
<!--begin::Row-->
<div class="row g-5">
    <!--begin::Col-->
    <div class="col-lg-12 mb-10">
        <!--begin::Lists Widget 19-->
        <div class="card card-flush">
            <!--begin::Header-->
            <div class="card-header pt-5">
                <!--begin::Title-->
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">Dashboard Helpdesk</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">Akses menu dashboard disini</span>
                </h3>
                <!--end::Title-->
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body pt-5">
                <!--begin::Item-->
                <div class="d-flex flex-stack">
                    <!--begin::Title-->
                    <a href="{{ url('/helpdesk/trouble_ticket')}}" class="text-danger opacity-75-hover fs-6 fw-semibold">Trouble Ticket</a>
                    <!--end::Title-->
                    <!--begin::Action-->
                    <a href="{{ url('/helpdesk/trouble_ticket')}}" class="btn btn-icon btn-sm h-auto btn-color-gray-500 btn-active-color-danger justify-content-end">
                        <i class="ki-outline ki-exit-right-corner fs-2"></i>
                    </a>
                    <!--end::Action-->
                </div>
                <!--end::Item-->
                <!--begin::Separator-->
                <div class="separator separator-dashed my-3"></div>
                <!--end::Separator-->
                <!--begin::Item-->
                <div class="d-flex flex-stack">
                    <!--begin::Title-->
                    <a href="{{ url('/helpdesk/maintenance_order')}}" class="text-danger opacity-75-hover fs-6 fw-semibold">Maintenance Order</a>
                    <!--end::Title-->
                    <!--begin::Action-->
                    <a href="{{ url('/helpdesk/maintenance_order')}}" class="btn btn-icon btn-sm h-auto btn-color-gray-500 btn-active-color-danger justify-content-end">
                        <i class="ki-outline ki-exit-right-corner fs-2"></i>
                    </a>
                    <!--end::Action-->
                </div>
                <!--end::Item-->
                <!--begin::Separator-->
                <div class="separator separator-dashed my-3"></div>
                <!--end::Separator-->
            </div>
            <!--end::Body-->
        </div>
    </div>
</div>
@stop 
<style>
#texto {
    color:#181C32 !important;
}

#logoadmin {
    width:50px;
    height:50px;
}
</style>
@section('styles')

