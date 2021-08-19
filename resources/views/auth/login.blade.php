<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description"
        content="Sistem Operasional Manajemen Kerja dan Perencaan">
    <meta name="author" content="pixelcave">
    <meta name="robots" content="noindex, nofollow">

    <title>
        @yield('title', config('app.name'))
    </title>
    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="shortcut icon" href="{{ asset('img/icon_1.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('img/icon_1.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/icon_1.png') }}">
    <!-- END Icons -->

    <!-- Stylesheets -->
    <!-- Fonts and OneUI framework -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" id="css-main" href="{{ asset('css/oneui.min.css') }}">

    <style>
        body {
            background-repeat: no-repeat;
            background-size: cover;
            background-image: url({{ asset('img/bg_office.jpg') }});
        }
    </style>

    <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
    <!-- END Stylesheets -->
</head>

<body>

    <div id="page-container">

        <!-- Main Container -->
        <main id="main-container">
            <!-- Page Content -->
            <div class="hero-static d-flex align-items-center">
                <div class="w-100">
                    <!-- Sign In Section -->
                    <div class="bg-white">
                        <div class="content content-full">
                            <div class="row justify-content-center">
                                <div class="col-md-8 col-lg-6 col-xl-4 pt-4">
                                    <!-- Header -->
                                    <div class="text-center">
                                        <img src="{{ asset('img/main_logo_color.png') }}" class="img-fluid"/>
                                        {{-- <p class="mb-2">
                                            <i class="fa fa-2x fa-circle-notch text-primary"></i>
                                        </p>
                                        <h1 class="h4 mb-1">
                                            Masuk
                                        </h1>
                                        <h2 class="h6 font-w400 text-muted mb-3">
                                            Sistem Operasi Managemen Kerja Perencanaan
                                        </h2> --}}
                                    </div>
                                    <!-- END Header -->

                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <div class="py-3">
                                            {{-- <div class="form-group">
                                                <input type="email"
                                                    class="form-control form-control-lg form-control-alt @error('email') is-invalid @enderror"
                                                    id="email" name="email" placeholder="e-mail"
                                                    value="{{ old('email') }}" required autocomplete="email"
                                                    autofocus>
                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div> --}}
                                            <div class="form-group">
                                                <input type="text"
                                                    class="form-control form-control-lg form-control-alt @error('username') is-invalid @enderror"
                                                    id="username" name="username" placeholder="username atau email"
                                                    value="{{ old('username') }}" required autocomplete="username"
                                                    autofocus>
                                                @error('username')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <input type="password"
                                                    class="form-control form-control-lg form-control-alt  @error('password') is-invalid @enderror"
                                                    id="password" name="password" placeholder="Password">
                                                @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            {{-- <div class="form-group">
                                                <div class="d-md-flex align-items-md-center justify-content-md-between">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            name="remember" id="remember"
                                                            {{ old('remember') ? 'checked' : '' }}">
                                                        <label class="custom-control-label font-w400"
                                                            for="remember">Remember Me</label>
                                                    </div>
                                                    @if (Route::has('password.request'))
                                                        <div class="py-2">
                                                            <a class="font-size-sm font-w500"
                                                                href="{{ route('password.request') }}">Forgot
                                                                Password?</a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div> --}}
                                        </div>
                                        <div class="form-group row justify-content-center mb-0">
                                            <div class="col-md-6 col-xl-5">
                                                <button type="submit" class="btn btn-block btn-primary">
                                                    <i class="fa fa-fw fa-sign-in-alt mr-1"></i> Masuk
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- END Sign In Form -->
                                    
                                    <!-- Footer -->
                                    <div class="font-size-sm text-center text-muted pt-3 mt-4">
                                        <img src="{{ asset('img/pemprov.png') }}" class="img-fluid mr-2" style="height: 50px;"/>
                                        <img src="{{ asset('img/disbun.png') }}" class="img-fluid" style="height: 50px;"/>
                                        {{-- <strong>Dinas Perkebunan - Perencanaan</strong> &copy; <span data-toggle="year-copy"></span> --}}
                                        <div>&copy; <span data-toggle="year-copy"></span></div>
                                    </div>
                                    <!-- END Footer -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Sign In Section -->
                </div>
            </div>
            <!-- END Page Content -->
        </main>
        <!-- END Main Container -->
    </div>
    <!-- END Page Container -->
    <script src="{{ asset('js/oneui.core.min.js') }}"></script>
    <script src="{{ asset('js/oneui.app.js') }}"></script>

    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>

    <!-- Page JS Code -->
    {{-- <script src="{{ asset('js/pages/op_auth_signin.min.js') }}"></script> --}}
</body>

</html>
