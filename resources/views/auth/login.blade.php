@extends('layouts.blank')

@section('title', 'Login')

@section('content')
<!--begin::Authentication - Sign-in -->
<div class="d-flex flex-column flex-lg-row flex-column-fluid">
    <!--begin::Aside-->
    <div class="d-flex flex-lg-row-fluid">
        <!--begin::Content-->
        <div class="d-flex flex-column flex-center pb-0 pb-lg-10 p-10 w-100">
            <!--begin::Image-->
            <img class="mx-auto mw-100 w-150px w-lg-400px mb-10 mb-lg-20" src="{{ asset('assets/_dwh/logo.png')}}" alt="" />
            {{-- theme-light-show <img class="theme-dark-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20" src="{{ asset('assets/media/auth/agency-dark.png')}}" alt="" /> --}}
            <!--end::Image-->
            <!--begin::Title-->
            {{-- <h1 class="text-gray-800 fs-2qx fw-bold text-center mb-7">SPECTRUM</h1> --}}
            <!--end::Title-->
            {{-- <img class="mx-auto mw-100 h-300px" src="{{ asset('assets/_pmo/spectrum.png')}}" alt="" /> --}}
            <!--begin::Text-->
            {{-- <div class="text-gray-600 fs-base text-center fw-semibold"><span class="text-danger">IT Core Values</span>
            <br />Securitization Perseverance Expansion Collaboration Transformation Regulation Utilization Monetization</div> --}}
            <!--end::Text-->
        </div>
        <!--end::Content-->
    </div>
    <!--begin::Aside-->
    <!--begin::Body-->
    <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">
        <!--begin::Wrapper-->
        <div class="bg-body d-flex flex-column flex-center rounded-4 w-md-600px p-10">
            <!--begin::Content-->
            <div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-md-400px">
                <!--begin::Wrapper-->
                <div class="d-flex flex-center flex-column-fluid pb-15 pb-lg-20">
                    <!--begin::Form-->
                    <form method="POST" class="form w-100" novalidate="novalidate" id="kt_sign_in_form" action="{{ url('/login')}}">
                        @csrf
                        <!--begin::Heading-->
                        <div class="text-center mb-11">
                            <!--begin::Title-->
                            <h1 class="text-dark fw-bolder mb-3">Login</h1>
                            <!--end::Title-->
                            <!--begin::Subtitle-->
                            <div class="text-gray-500 fw-semibold fs-6">ke DWH Monitoring</div>
                            <!--end::Subtitle=-->
                        </div>
                        <!--begin::Heading-->
                        @if (session('error'))
                        <p class="text-danger fs-6 mb-5">{{ session('error') }}</p>
                        @endif
                        <!--begin::Input group=-->
                        <div class="fv-row mb-8">
                            <!--begin::Email-->
                            <input type="text" placeholder="NIK TG" name="username" autocomplete="off" class="form-control bg-transparent" />
                            <!--end::Email-->
                        </div>
                        <!--end::Input group=-->
                        <!--end::Input group=-->
                        <div class="fv-row mb-3">
                            <div class="input-group mb-5">
                                <input id="password" type="password" class="form-control bg-transparent" name="password" placeholder="Password" autocomplete="off"/>
                                <button type="button" class="input-group-text" onclick="show_hide()">
                                    <i class="ki-duotone ki-eye show-hide">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </button>
                            </div>
                            <!--end::Password-->
                        </div>
                        <!--end::Input group=-->
                        <!--begin::Wrapper-->
                        <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                            <div></div>
                            <!--begin::Link-->
                            <a href="https://portal.telkom.co.id/login/forgot" class="link-danger">Lupa Password ?</a>
                            <!--end::Link-->
                        </div>
                        <!--end::Wrapper-->
                        <!--begin::Submit button-->
                        <div class="d-grid mb-10">
                            <button type="submit" id="kt_sign_in_submit" class="btn btn-danger">
                                <!--begin::Indicator label-->
                                <span class="indicator-label">Sign In</span>
                                <!--end::Indicator label-->
                                <!--begin::Indicator progress-->
                                <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                <!--end::Indicator progress-->
                            </button>
                        </div>
                        <!--end::Submit button-->
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Wrapper-->
                <!--begin::Footer-->
                <div class="d-flex flex-end">
                    <!--begin::Links-->
                    <div class="d-flex fw-semibold text-primary fs-base gap-5">
                        <a href="https://mitratel.co.id" class="text-danger" target="_blank">Mitratel</a>
                        <a href="https://sharepoint.mitratel.co.id" class="text-danger" target="_blank">Sharepoint</a>
                        <a href="mailto:admin@mitratel.co.id" class="text-danger" target="_blank">Email to Admin</a>
                    </div>
                    <!--end::Links-->
                </div>
                <!--end::Footer-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Body-->
</div>
<!--end::Authentication - Sign-in-->
@stop

@section('styles')
<style>
    body { background-image: url("{{ asset('assets/media/auth/bg10.jpeg')}}"); } 
    [data-bs-theme="dark"] body { background-image: url("{{ asset('assets/media/auth/bg10-dark.jpeg')}}"); }
</style>
@stop

@section('scripts')
<script>
    var KTSigninGeneral = (function () {
    var e, t, i;
    return {
        init: function () {
        (e = document.querySelector("#kt_sign_in_form")),
            (t = document.querySelector("#kt_sign_in_submit")),
            (i = FormValidation.formValidation(e, {
            fields: {
                email: {
                    validators: {
                        notEmpty: { message: "NIK TG tidak boleh kosong" },
                    },
                },
                password: {
                    validators: { notEmpty: { message: "Password tidak boleh kosong" } },
                },
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5({
                rowSelector: ".fv-row",
                eleInvalidClass: "",
                eleValidClass: "",
                }),
            },
            })),
            t.addEventListener("click", function (n) {
            n.preventDefault(),
                i.validate().then(function (i) {
                "Valid" == i
                    ? e.submit()
                    : Swal.fire({
                        text: "Maaf, sepertinya ada kesalahan dalam inputan anda. Mohon cek kembali inputan anda.",
                        icon: "error",
                        buttonsStyling: !1,
                        confirmButtonText: "Baik",
                        customClass: { confirmButton: "btn btn-primary" },
                    });
                });
            });
        },
    };
    })();
    KTUtil.onDOMContentLoaded(function () {
    KTSigninGeneral.init();
    });
    function show_hide() {
        var x = document.getElementById("password");
        var y = $(".show-hide");
        if (x.type === "password") {
            x.type = "text";
            y.removeClass("ki-eye");
            y.addClass("ki-eye-slash");
        } else {
            x.type = "password";
            y.addClass("ki-eye");
            y.removeClass("ki-eye-slash");
        }
    }
</script>
@stop
