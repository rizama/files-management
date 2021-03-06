<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', config('app.name'))
    </title>
    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="shortcut icon" href="{{ asset('img/icon_1.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('img/icon_1.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/icon_1.png') }}">
    <!-- END Icons -->


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    @yield('css')

    <!-- Theme Styles -->
    <link href="{{ asset('css/oneui.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">

    <!-- Custom Styles -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    @yield('css_custom')

    @yield('js_before')
</head>

<body>
    <div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow page-header-dark sidebar-mini">
        @php
            if (Auth::user()->role->code == 'superadmin') {
                $homeURL = url('/users');
            } else if (Auth::user()->role->code == 'level_1' || Auth::user()->role->code == 'level_2') {
                $homeURL = url('/dashboard');
            } else {
                $homeURL = url('/file_publics/search');
            }
        @endphp
        <nav id="sidebar" aria-label="Main Navigation">
            <!-- Side Header -->
            <div class="content-header bg-white-5">
                <!-- Logo -->
                <a class="font-w600 text-dual" href="{{ $homeURL }}">
                    <span class="smini-visible" style="margin-left: -8px;">
                        <img src="{{ asset('img/icon_light.png') }}" style="width: 32px" />
                        {{-- <i class="fa fa-circle-notch text-primary"></i> --}}
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
                                    <span class="nav-main-link-name">Pengguna</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link {{ Request::is('roles*') ? 'active' : '' }}" href="{{ url('/roles') }}">
                                    <i class="nav-main-link-icon fa fa-code-branch"></i>
                                    <span class="nav-main-link-name">Role</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link {{ Request::is('categories*') ? 'active' : '' }}" href="{{ url('/categories') }}">
                                    <i class="nav-main-link-icon si si-grid"></i>
                                    <span class="nav-main-link-name">Kategori</span>
                                </a>
                            </li>
                        @else
                            @if (Auth::user()->role->code == 'level_1' || Auth::user()->role->code == 'level_2')
                                <li class="nav-main-item">
                                    <a class="nav-main-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                                        <i class="nav-main-link-icon si si-speedometer"></i>
                                        <span class="nav-main-link-name">Beranda</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link {{ Request::is('tasks*') ? 'active' : '' }}" href="{{ url('/tasks') }}">
                                        <i class="nav-main-link-icon si si-briefcase"></i>
                                        <span class="nav-main-link-name">Tugas</span>
                                    </a>
                                </li>
                                @if (Auth::user()->role->code == 'level_2')
                                    <li class="nav-main-item">
                                        <a class="nav-main-link {{ Request::is('mytasks*') ? 'active' : '' }}" href="{{ route('tasks.my_task') }}">
                                            <i class="nav-main-link-icon fa fa-tasks"></i>
                                            @if ($notif_count && $notif_count > 0)
                                                <span class="badge badge-pill badge-danger text-white" style="left: 25px; top: 22px; position: absolute; font-size: 8px;">{{ $notif_count }}</span>
                                            @endif
                                            <span class="nav-main-link-name">Tugas Saya</span>
                                        </a>
                                    </li>
                                @endif
                                <li class="nav-main-item">
                                    <a class="nav-main-link {{ Request::is('search*') ? 'active' : '' }}" href="{{ url('/search') }}">
                                        <i class="nav-main-link-icon si si-magnifier"></i>
                                        <span class="nav-main-link-name">Pencarian</span>
                                    </a>
                                </li>
                            @endif
                            @if (Auth::user()->role->code == 'guest')
                                <li class="nav-main-item">
                                    <a class="nav-main-link {{ Request::is('file_publics/search*') ? 'active' : '' }}" href="{{ url('/file_publics/search') }}">
                                        <i class="nav-main-link-icon si si-magnifier"></i>
                                        <span class="nav-main-link-name">Pencarian</span>
                                    </a>
                                </li>
                            @endif
                            @if (Auth::user()->role->code == 'level_1')
                                <li class="nav-main-item">
                                    <a class="nav-main-link {{ Request::is('file_publics*') ? 'active' : '' }}" href="{{ url('/file_publics') }}">
                                        <i class="nav-main-link-icon si si-notebook"></i>
                                        <span class="nav-main-link-name">Dokumen Umum</span>
                                    </a>
                                </li>
                            @endif
                        @endif
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
                    <a class="navbar-brand text-light font-weight-bold" href="{{ $homeURL }}">
                        Sistem Operasional Manajemen Kerja dan Perencanaan
                    </a>
                </div>
                <!-- Right Section -->
                <div class="d-flex align-items-center">
                    <!-- Notifications Dropdown -->
                    <div class="dropdown d-inline-block ml-2">
                        @if (Auth::user()->role->code == 'level_1' || Auth::user()->role->code == 'level_2')
                            <button type="button" class="btn btn-sm btn-dual" id="page-header-notifications-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-fw fa-bell"></i>
                                <span class="badge badge-pill badge-danger text-white">{{ $notif_count }}</span>
                            </button>
                        @endif
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0 border-0 font-size-sm" aria-labelledby="page-header-notifications-dropdown">
                            <div class="p-2 bg-primary-dark text-center rounded-top">
                                <h5 class="dropdown-header text-uppercase text-white">Notifikasi</h5>
                            </div>
                            <ul class="nav-items mb-0" style="max-height: 300px; overflow-y: auto;">
                                @forelse ($notif_content as $notif)
                                    @if (Auth::user()->role->code == 'level_1')
                                    <li>
                                        <a class="text-dark media py-2" href="{{ url('/tasks/show/').'/'.encrypt($notif['task_id']) }}">
                                            <div class="mr-2 ml-3 align-self-center">
                                                <i class="fa fa-fw fa-file-alt text-primary"></i>
                                            </div>
                                            <div class="media-body pr-2">
                                                <div class="font-w600 clamp-1">{{ $notif['info'] }}</div>
                                                <div class="font-w500 text-muted" title="{{ $notif['file'] }}">
                                                    {{ $notif['user'] }}
                                                    <footer class="blockquote-footer clamp clamp-1 break-all">{{ $notif['file'] }}</footer>
                                                </div>
                                                <span class="font-w400 text-muted">{{ \Carbon\Carbon::parse($notif['created_at'])->diffForHumans() }}</span>
                                            </div>
                                        </a>
                                    </li>
                                    @elseif(Auth::user()->role->code == 'level_2')
                                    <li>
                                        <a class="text-dark media py-2" href="{{ url('/tasks/show/').'/'.encrypt($notif['task_id']) }}">
                                            <div class="mr-2 ml-3 align-self-center">
                                                <i class="fa fa-fw fa-file-alt text-primary"></i>
                                            </div>
                                            <div class="media-body pr-2">
                                                <div class="font-w600 clamp-1">{{ $notif['info'] }}</div>
                                                <div class="font-w500 text-muted" title="{{ $notif['task'] }}">
                                                    {{ $notif['task'] }}
                                                    <footer class="blockquote-footer clamp clamp-1 break-all">{{ $notif['creator'] }}</footer>
                                                </div>
                                                <span class="font-w400 text-muted">{{ \Carbon\Carbon::parse($notif['created_at'])->diffForHumans() }}</span>
                                            </div>
                                        </a>
                                    </li>
                                    @endif
                                @empty
                                    <li>
                                        <div class="text-dark media py-2">
                                            <div class="media-body mx-2">
                                                @if (Auth::user()->role->code == 'level_1')
                                                    <center><span class="font-w500 text-muted clamp-1">Tidak ada Dokumen yang menunggu persetujuan</span></center>
                                                @else
                                                    <span class="font-w500 text-muted clamp-1">Tidak ada Tugas yang belum selesai</span>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforelse
                            </ul>
                            @if (count($notif_content) > 0)
                                <div class="p-2 border-top">
                                    <a class="btn btn-sm btn-light btn-block text-center"  href="{{ route('notifications')}}">
                                        <i class="fa fa-fw fa-arrow-down mr-1"></i> Lihat Semua
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- END Notifications Dropdown -->

                    <!-- User Dropdown -->
                    <div class="dropdown d-inline-block ml-2">
                        <button type="button" class="btn btn-sm btn-dual d-flex align-items-center" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle" src="{{ asset('media/avatars/avatar10.jpg') }}" alt="Header Avatar" style="width: 21px;">
                            <span class="d-none d-sm-inline-block ml-2">{{ Auth::user()->name }}</span>
                            <i class="fa fa-fw fa-angle-down d-none d-sm-inline-block ml-1 mt-1"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-right p-0 border-0" aria-labelledby="page-header-user-dropdown">
                            <div class="p-3 text-center bg-primary-dark rounded-top">
                                <img class="img-avatar img-avatar48 img-avatar-thumb" src="{{ asset('media/avatars/avatar10.jpg') }}" alt="">
                                <p class="mt-2 mb-0 text-white font-w500">{{ Auth::user()->username }}</p>
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
                    <!-- END User Dropdown -->
                </div>
            </div>
            <!-- END Right Section -->
        </header>
        <!-- END Header Content -->

        <!-- Main Container -->
        <main id="main-container">
            <div class="bg-body-light-transparent">
                <div class="content content-full">
                    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                        <h1 class="flex-sm-fill h3 my-2">
                            @hasSection('child-breadcrumb')
                                @yield('child-breadcrumb')
                            @endif
                            @sectionMissing('child-breadcrumb')
                                @yield('page-title', 'Halaman') 
                            @endif
                            @hasSection('info-page-title')
                                <small class="d-block d-sm-inline-block mt-2 mt-sm-0 font-size-base font-w400 text-muted">@yield('info-page-title')</small>
                            @endif
                        </h1>
                        @hasSection('child-breadcrumb')
                            <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-alt">
                                    <li class="breadcrumb-item" aria-current="page">
                                        <a class="link-fx" href="@yield('breadcrumb-url', url()->previous())">@yield('page-title')</a>
                                    </li>
                                    <li class="breadcrumb-item">@yield('child-breadcrumb')</li>
                                </ol>
                            </nav>
                        @endif
                    </div>
                </div>
            </div>
            <div class="content" style="padding-top: 1rem;">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer id="page-footer" class="bg-body-light-transparent">
            <div class="content py-3">
                <div class="row font-size-sm">
                    <div class="col-sm-6 order-sm-1 py-1 text-center text-sm-left">
                        <a class="font-w600" href="{{ $homeURL }}">{{ env('APP_NAME') }}</a> &copy; <span data-toggle="year-copy"></span>
                    </div>
                </div>
            </div>
        </footer>
        <!-- END Footer -->
    </div>

    {{-- JS Section --}}
    <!-- OneUI Core JS -->
    <script src="{{ asset('js/oneui.core.min.js') }}"></script>
    <script src="{{ asset('js/oneui.app.min.js') }}"></script>

    <!-- Scripts -->
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/swal2.js') }}"></script>
    @yield('js_after')
</body>

</html>
