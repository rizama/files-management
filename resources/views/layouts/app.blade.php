<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', config('app.name', 'SIMANTAP'))
    </title>
    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="shortcut icon" href="{{ asset('oneui/src/assets/media/favicons/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('oneui/src/assets/media/favicons/favicon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('oneui/src/assets/media/favicons/apple-touch-icon-180x180.png') }}">
    <!-- END Icons -->


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Theme Styles -->
    <link href="{{ asset('css/oneui.min.css') }}" rel="stylesheet">

    <!-- Custom Styles -->
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}
    @yield('css')

    @yield('js_before')
</head>

<body>
    <div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
        <nav id="sidebar" aria-label="Main Navigation">
            <!-- Side Header -->
            <div class="content-header bg-white-5">
                <!-- Logo -->
                <a class="font-w600 text-dual" href="#">
                    <span class="smini-visible">
                        <i class="fa fa-circle-notch text-primary"></i>
                    </span>
                    <span class="smini-hide font-size-h5 tracking-wider">
                        <span class="font-w900">{{ env('APP_NAME') }}</span> 
                    </span>
                </a>
                <!-- END Logo -->
            </div>
            <!-- END Side Header -->

            <!-- Sidebar Scrolling -->
            <div class="js-sidebar-scroll">
                <!-- Side Navigation -->
                <div class="content-side">
                    <ul class="nav-main">
                        @if (Auth::user()->role->code == 'superadmin')
                            <li class="nav-main-item">
                                <a class="nav-main-link {{ Request::is('users*') ? 'active' : '' }}" href="{{ url('/users') }}">
                                    <i class="nav-main-link-icon si si-users"></i>
                                    <span class="nav-main-link-name">Users</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link {{ Request::is('roles') ? 'active' : '' }}" href="{{ url('/users') }}">
                                    <i class="nav-main-link-icon fa fa-code-branch"></i>
                                    <span class="nav-main-link-name">Roles</span>
                                </a>
                            </li>
                        @else
                            <li class="nav-main-item">
                                <a class="nav-main-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                                    <i class="nav-main-link-icon si si-speedometer"></i>
                                    <span class="nav-main-link-name">Dashboard</span>
                                </a>
                            </li>
                        @endif
                        {{-- <li class="nav-main-item">
                            <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
                                <i class="nav-main-link-icon si si-layers"></i>
                                <span class="nav-main-link-name">Page Packs</span>
                            </a>
                            <ul class="nav-main-submenu">
                                <li class="nav-main-item">
                                    <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
                                        <i class="nav-main-link-icon si si-bag"></i>
                                        <span class="nav-main-link-name">e-Commerce</span>
                                    </a>
                                    <ul class="nav-main-submenu">
                                        <li class="nav-main-item">
                                            <a class="nav-main-link" href="be_pages_ecom_dashboard.html">
                                                <span class="nav-main-link-name">Dashboard</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li> --}}
                    </ul>
                </div>
                <!-- END Side Navigation -->
            </div>
            <!-- END Sidebar Scrolling -->
        </nav>
        <!-- END Sidebar -->

        <!-- Header -->
        <header id="page-header">
            <!-- Header Content -->
            <div class="content-header">

                <!-- Left Section -->
                <div class="d-flex align-items-center">
                    <!-- Toggle Sidebar -->
                    <!-- Layout API, functionality initialized in Template._uiApiLayout()-->
                    <button type="button" class="btn btn-sm btn-dual mr-2 d-lg-none" data-toggle="layout" data-action="sidebar_toggle">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>
                    <!-- END Toggle Sidebar -->
                    <!-- Toggle Mini Sidebar -->
                    <!-- Layout API, functionality initialized in Template._uiApiLayout()-->
                    <button type="button" class="btn btn-sm btn-dual mr-2 d-none d-lg-inline-block" data-toggle="layout" data-action="sidebar_mini_toggle">
                        <i class="fa fa-fw fa-ellipsis-v"></i>
                    </button>
                    <!-- END Toggle Mini Sidebar -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        Sistem Manajemen Tugas Perencaan dan Pelaporan
                    </a>
                </div>
                <!-- Right Section -->
                <div class="d-flex align-items-center">
                    <!-- User Dropdown -->
                    <div class="dropdown d-inline-block ml-2">
                        <button type="button" class="btn btn-sm btn-dual d-flex align-items-center" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle" src="{{ asset('oneui/src/assets/media/avatars/avatar10.jpg') }}" alt="Header Avatar" style="width: 21px;">
                            <span class="d-none d-sm-inline-block ml-2">{{ Auth::user()->email }}</span>
                            <i class="fa fa-fw fa-angle-down d-none d-sm-inline-block ml-1 mt-1"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-right p-0 border-0" aria-labelledby="page-header-user-dropdown">
                            <div class="p-3 text-center bg-primary-dark rounded-top">
                                <img class="img-avatar img-avatar48 img-avatar-thumb" src="{{ asset('oneui/src/assets/media/avatars/avatar10.jpg') }}" alt="">
                                <p class="mt-2 mb-0 text-white font-w500">{{ Auth::user()->name }}</p>
                                {{-- <p class="mb-0 text-white-50 font-size-sm">Web Developer</p> --}}
                            </div>
                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('logout') }}" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                                <span class="font-size-sm font-w500">Log Out</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
                <!-- END User Dropdown -->
            </div>
            <!-- END Right Section -->
        </header>
        <!-- END Header Content -->

        <!-- Main Container -->
        <main id="main-container">
            <div class="content">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer id="page-footer" class="bg-body-light">
            <div class="content py-3">
                <div class="row font-size-sm">
                    <div class="col-sm-6 order-sm-2 py-1 text-center text-sm-right">
                        {{-- Crafted with <i class="fa fa-heart text-danger"></i> by <a class="font-w600" href="https://1.envato.market/ydb" target="_blank">pixelcave</a> --}}
                    </div>
                    <div class="col-sm-6 order-sm-1 py-1 text-center text-sm-left">
                        <a class="font-w600"  target="_blank">SIMANTAP</a> &copy; <span data-toggle="year-copy"></span>
                    </div>
                </div>
            </div>
        </footer>
        <!-- END Footer -->
    </div>

    {{-- JS Section --}}
    <!-- OneUI Core JS -->
    <script src="{{ asset('js/oneui.app.js') }}"></script>

    <!-- Scripts -->
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
    @yield('js_after')
</body>

</html>
