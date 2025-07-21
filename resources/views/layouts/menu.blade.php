@php 
    $separator = session()->get('url_separator').'/';
    $path = $_SERVER['REQUEST_URI'];
    if ($separator != '/'){
        $path_short = explode($separator, $path);
    } else {
        $path_short[1] = substr($path, 1); 
    }
    $path_cut_1 = explode('?', $path_short[1]);
    $path_cut_2 = explode('#', $path_cut_1[0]);
    $path_current = explode('/', $path_cut_2[0]);
@endphp
<div id="kt_app_sidebar" class="app-sidebar" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="auto" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::Primary menu-->
    <div id="kt_aside_menu_wrapper" class="app-sidebar-menu flex-grow-1 hover-scroll-y scroll-lg-ps my-5 pt-8" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px">
        <!--begin::Menu-->
        <div id="kt_aside_menu" class="menu menu-rounded menu-column menu-title-gray-600 menu-state-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500 fw-semibold fs-6" data-kt-menu="true">
            @if ((session()->get('user')->privilege == 'ADMIN') || (session()->get('user')->privilege == 'USER'))
            <!--begin:Menu item-->
            <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item  @if ($path_current[0] == 'dwh') here show @endif py-2">
                @if ($path_current[0] == 'dwh')
                <!--begin:Menu link-->
                <span class="menu-link menu-center theme-light-show bg-white">
                    <img src="{{ asset('assets/_dwh/icon.png')}}" class="mh-25px">
                </span>
                <span class="menu-link menu-center theme-dark-show bg-danger">
                    <img src="{{ asset('assets/_dwh/icon-white.png')}}" class="mh-25px">
                </span>
                <!--end:Menu link-->
                @else
                <!--begin:Menu link-->
                <span class="menu-link menu-center theme-light-show bg-secondary bg-hover-white">
                    <img src="{{ asset('assets/_dwh/icon-gray.png')}}" class=" mh-25px">
                </span>
                <span class="menu-link menu-center theme-dark-show bg-secondary">
                    <img src="{{ asset('assets/_dwh/icon-white.png')}}" class="mh-25px">
                </span>
                <!--end:Menu link-->
                @endif
                <!--begin:Menu sub-->
                <div class="menu-sub menu-sub-dropdown px-2 py-4 w-250px mh-75 overflow-auto">
                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu content-->
                        <div class="menu-content">
                            <span class="menu-section fs-5 fw-bolder ps-1 py-1">DWH</span>
                        </div>
                        <!--end:Menu content-->
                    </div>
                    <!--end:Menu item-->
                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link @if (($path_current[0] == 'dwh') && ($path_current[1] == 'dashboard')) active @endif" href="{{ url('/dwh/dashboard') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Dashboard DWH</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->
                </div>
                <!--end:Menu sub-->
            </div>
            <!--end:Menu item-->
            @endif
            @if (session()->get('user')->privilege == 'ADMIN')
            <!--begin:Menu item-->
            <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item  @if ($path_current[0] == 'admin') here show @endif py-2">
                @if ($path_current[0] == 'admin')
                <!--begin:Menu link-->
                <span class="menu-link menu-center theme-light-show bg-white">
                    <img src="{{ asset('assets/_dwh/icon.png')}}" class="mh-25px">
                </span>
                <span class="menu-link menu-center theme-dark-show bg-danger">
                    <img src="{{ asset('assets/_dwh/icon-white.png')}}" class="mh-25px">
                </span>
                <!--end:Menu link-->
                @else
                <!--begin:Menu link-->
                <span class="menu-link menu-center theme-light-show bg-secondary bg-hover-white">
                    <img src="{{ asset('assets/_dwh/icon-gray.png')}}" class=" mh-25px">
                </span>
                <span class="menu-link menu-center theme-dark-show bg-secondary">
                    <img src="{{ asset('assets/_dwh/icon-white.png')}}" class="mh-25px">
                </span>
                <!--end:Menu link-->
                @endif
                <!--begin:Menu sub-->
                <div class="menu-sub menu-sub-dropdown px-2 py-4 w-250px mh-75 overflow-auto">
                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu content-->
                        <div class="menu-content">
                            <span class="menu-section fs-5 fw-bolder ps-1 py-1">Admin</span>
                        </div>
                        <!--end:Menu content-->
                    </div>
                    <!--end:Menu item-->
                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link @if (($path_current[0] == 'admin') && ($path_current[1] == 'dashboard_admin')) active @endif" href="{{ url('/admin/dashboard_admin') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Dashboard Admin</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->
                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link @if (($path_current[0] == 'admin') && (($path_current[1] == 'manage_users') || ($path_current[1] == 'detail_users'))) active @endif" href="{{ url('/admin/manage_users') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Manage Users</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->
                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link @if (($path_current[0] == 'admin') && (($path_current[1] == 'manage_airflow_table') || ($path_current[1] == 'detail_airflow_table'))) active @endif" href="{{ url('/admin/manage_airflow_table') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Manage Airflow Table</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->
                </div>
                <!--end:Menu sub-->
            </div>
            <!--end:Menu item-->
            @endif
            <!--begin:Menu item-->
            <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item  @if ($path_current[0] == 'helpdesk') here show @endif py-2">
                @if ($path_current[0] == 'helpdesk')
                <!--begin:Menu link-->
                <span class="menu-link menu-center theme-light-show bg-white">
                    <img src="{{ asset('assets/_dwh/icon.png')}}" class="mh-25px">
                </span>
                <span class="menu-link menu-center theme-dark-show bg-danger">
                    <img src="{{ asset('assets/_dwh/icon-white.png')}}" class="mh-25px">
                </span>
                <!--end:Menu link-->
                @else
                <!--begin:Menu link-->
                <span class="menu-link menu-center theme-light-show bg-secondary bg-hover-white">
                    <img src="{{ asset('assets/_dwh/icon-gray.png')}}" class=" mh-25px">
                </span>
                <span class="menu-link menu-center theme-dark-show bg-secondary">
                    <img src="{{ asset('assets/_dwh/icon-white.png')}}" class="mh-25px">
                </span>
                <!--end:Menu link-->
                @endif
                <!--begin:Menu sub-->
                <div class="menu-sub menu-sub-dropdown px-2 py-4 w-250px mh-75 overflow-auto">
                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu content-->
                        <div class="menu-content">
                            <span class="menu-section fs-5 fw-bolder ps-1 py-1">Helpdesk</span>
                        </div>
                        <!--end:Menu content-->
                    </div>
                    <!--end:Menu item-->
                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link @if (($path_current[0] == 'helpdesk') && ($path_current[1] == 'dashboard')) active @endif" href="{{ url('/helpdesk/dashboard') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Dashboard Portal</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->
                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link @if (($path_current[0] == 'helpdesk') && ($path_current[1] == 'trouble_ticket')) active @endif" href="{{ url('/helpdesk/trouble_ticket') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Trouble Ticket</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->
                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link @if (($path_current[0] == 'helpdesk') && ($path_current[1] == 'maintenance_order')) active @endif" href="{{ url('/helpdesk/maintenance_order') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Maintenance Order</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->
                </div>
                <!--end:Menu sub-->
            </div>
            <!--end:Menu item-->
        </div>
        <!--end::Menu-->
    </div>
    <!--end::Primary menu-->
    <!--begin::Footer-->
    <div class="d-flex flex-column flex-center pb-4 pb-lg-8" id="kt_app_sidebar_footer">
        <!--begin::Menu toggle-->
        <a href="#" class="btn btn-icon btn-active-color-danger" data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
            <i class="ki-outline ki-night-day theme-light-show fs-2x"></i>
            <i class="ki-outline ki-moon theme-dark-show fs-2x"></i>
        </a>
        <!--begin::Menu toggle-->
        <!--begin::Menu-->
        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px" data-kt-menu="true" data-kt-element="theme-mode-menu">
            <!--begin::Menu item-->
            <div class="menu-item px-3 my-0">
                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                    <span class="menu-icon" data-kt-element="icon">
                        <i class="ki-outline ki-night-day fs-2"></i>
                    </span>
                    <span class="menu-title">Light</span>
                </a>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu item-->
            <div class="menu-item px-3 my-0">
                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                    <span class="menu-icon" data-kt-element="icon">
                        <i class="ki-outline ki-moon fs-2"></i>
                    </span>
                    <span class="menu-title">Dark</span>
                </a>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu item-->
            <div class="menu-item px-3 my-0">
                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                    <span class="menu-icon" data-kt-element="icon">
                        <i class="ki-outline ki-screen fs-2"></i>
                    </span>
                    <span class="menu-title">System</span>
                </a>
            </div>
            <!--end::Menu item-->
        </div>
        <!--end::Menu-->
    </div>
    <!--end::Footer-->
</div>