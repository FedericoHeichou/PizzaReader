<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin | {{ config('settings.reader_name') }}</title>

    <!-- SEO -->
    <link rel="canonical" href="{{ URL::current() }}" />
    <meta name="description" content="{{ config('settings.description') }}"/>
    <meta property="og:image" content="{{ asset(config('settings.cover_path')) }}" />
    <meta property="og:image:secure_url" content="{{ asset(config('settings.cover_path')) }}" />
    <meta property="og:site_name" content="{{ config('settings.reader_name') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ config('settings.reader_name_long') }}" />
    <meta property="og:description" content="{{ config('settings.description') }}" />
    <meta property="og:url" content="{{ URL::current() }}" />

    <!-- Scripts -->
    <script src="{{ mix('js/bootstrap.js') }}" defer></script>
    <script src="{{ mix('js/admin.js') }}" defer></script>
    <script type="text/javascript">
        const LOGGED_IN = true;
    </script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ mix('css/admin.css') }}" rel="stylesheet">

    <!-- Browser info -->
    <link rel="icon" href="{{ config('settings.logo_asset_72') }}" sizes="32x32"/>
    <link rel="icon" href="{{ config('settings.logo_asset_72') }}" sizes="72x72"/>
    <link rel="icon" href="{{ config('settings.logo_asset_192') }}" sizes="192x192"/>
    <link rel="apple-touch-icon" href="{{ config('settings.logo_asset_72') }}"/>
    <meta name="msapplication-TileImage" content="{{ config('settings.logo_asset_72') }}"/>
    <link rel="manifest" href="{{ asset('manifest.json') }}" crossOrigin="use-credentials">
</head>
<body class="{{ isset($_COOKIE["dark"]) &&  $_COOKIE["dark"] ? "dark" : "" }}">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container px-lg-0">
                <a class="navbar-brand" href="{{ config('settings.home_link', url('/')) }}">
                    @if(config('settings.logo'))
                        <img alt="Logo of {{ config('settings.reader_name', 'PizzaReader') }}"
                             title="Logo of {{ config('settings.reader_name', 'PizzaReader') }}"
                             class="logo" src="{{ config('settings.logo_asset_72') }}">
                    @endif
                    {{ config('settings.reader_name', 'PizzaReader') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link{{ Route::is('admin.comics.*') ? ' active' : '' }}"
                               href="{{ route('admin.comics.index') }}">
                                <span aria-hidden="true" title="Comics" class="fas fa-book fa-fw"></span> Comics
                            </a>
                        </li>
                        @if(Auth::check() && Auth::user()->hasPermission('manager'))
                            <li class="nav-item">
                                <a class="nav-link{{ Route::is('admin.users.*') ? ' active' : '' }}"
                                   href="{{ route('admin.users.index') }}">
                                    <span aria-hidden="true" title="Users" class="fas fa-users-cog fa-fw"></span> Users
                                </a>
                            </li>
                        @endif
                        @if(Auth::check() && Auth::user()->hasPermission('admin'))
                            <li class="nav-item">
                                <a class="nav-link{{ Route::is('admin.teams.*') ? ' active' : '' }}"
                                   href="{{ route('admin.teams.index') }}">
                                    <span aria-hidden="true" title="Teams" class="fas fa-users fa-fw"></span> Teams
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link{{ Route::is('admin.settings.*') ? ' active' : '' }}"
                                   href="{{ route('admin.settings.edit') }}">
                                    <span aria-hidden="true" title="Settings" class="fas fa-cog fa-fw"></span> Settings
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link"
                               href="{{ route('home') }}" target="_blank">
                                <span aria-hidden="true" title="Reader" class="fas fa-book-open fa-fw"></span> Reader
                            </a>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <div class="custom-control custom-switch p-2">
                                <input type="checkbox" class="custom-control-input" id="dark-mode-switch"
                                    {{ isset($_COOKIE["dark"]) &&  $_COOKIE["dark"] ? "checked" : "" }}>
                                <label class="custom-control-label" for="dark-mode-switch">Dark</label>
                            </div>
                        </li>
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    @if(Auth::user()->hasPermission('checker'))
                                        <a class="dropdown-item" href="{{ route('admin.comics.index') }}">
                                            <span aria-hidden="true" title="Admin panel" class="fas fa-wrench fa-fw"></span> Admin panel
                                        </a>
                                    @endif
                                    <a class="dropdown-item" href="{{ route('user.edit') }}">
                                        <span aria-hidden="true" title="Profile" class="fas fa-user fa-fw"></span> Edit profile
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <span aria-hidden="true" title="Sign-out" class="fas fa-sign-out-alt fa-fw"></span> {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="container-lg p-0 py-sm-4 overflow-hidden">
            @include('partials.breadcrumb')
            @include('partials.alerts')
            @yield('content')
        </main>
    </div>
    @include('partials.confirmbox')
    <div id="notification-zone"></div>
    @yield('script')
</body>
</html>
