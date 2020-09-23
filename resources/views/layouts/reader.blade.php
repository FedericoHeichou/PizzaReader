<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('settings.reader_name', 'PizzaReader') }}</title>

    <!-- Scripts -->
    <script type="text/javascript">
        const BASE_URL = "{{ substr(config('app.url'), -1) === '/' ? config('app.url') : config('app.url') . '/' }}"
        const API_BASE_URL = BASE_URL + 'api';
    </script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/card-search.js') }}" defer></script>
    <script src="{{ asset('js/frontend.js') }}" defer></script>
    <script src="{{ asset('js/jquery.touchSwipe.min.js') }}" defer></script>
    <script src="{{ asset('js/reader.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/frontend.css') }}" rel="stylesheet">
    <link href="{{ asset('css/reader.css') }}" rel="stylesheet">

    <!-- Browser info -->
    <link rel="icon" href="{{ config('settings.logo_path_72') }}" sizes="32x32"/>
    <link rel="icon" href="{{ config('settings.logo_path_72') }}" sizes="192x192"/>
    <link rel="apple-touch-icon" href="{{ config('settings.logo_path_72') }}"/>
    <meta name="msapplication-TileImage" content="{{ config('settings.logo_path_72') }}"/>
    <link rel="manifest" href="{{ asset('manifest.json') }}" crossOrigin="use-credentials">
</head>
<body>
    <div id="app">
        <nav id="header" class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container px-lg-0">
                <a class="navbar-brand" href="{{ config('settings.home_link', url('/')) }}">
                    @if(config('settings.logo'))
                        <img alt="Logo of {{ config('settings.reader_name', 'PizzaReader') }}"
                             title="Logo of {{ config('settings.reader_name', 'PizzaReader') }}"
                             class="logo" src="{{ config('settings.logo_path_72') }}">
                    @endif
                    {{ config('settings.reader_name', 'PizzaReader') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse"
                        data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false"
                        aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <router-link to="/comics" class="nav-link">
                                <span aria-hidden="true" title="Comics" class="fas fa-book fa-fw"></span> Comics
                            </router-link>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto col-4 pr-0">
                        <li class="nav-item" style="width: 100%">
                            <input id="nav-filter" class="form-control mr-sm-2 card-search"
                                   type="search" placeholder="Filter" aria-label="Filter" name="filter"
                                   style="display: none" autocomplete="off">
                            <input id="nav-search" type="search" placeholder="Search comic" aria-label="Search comic"
                                   name="search" class="form-control mr-sm-2"
                                   style="display: none" autocomplete="off">
                            <div id="results-box" style="display: none"></div>
                        </li>
                        @if(Auth::check())
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
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
        <main class="container-lg p-0 overflow-hidden">
            <router-view>
                <div style="text-align:center;position:absolute;top:0;left:0;width:100%;height:100%;background-color:#6cb2eb;">
                    <div style="margin-top:50px;color:#fff;">
                        @if(config('settings.logo'))
                            <img alt="Logo of {{ config('settings.reader_name', 'PizzaReader') }}"
                                 title="Logo of {{ config('settings.reader_name', 'PizzaReader') }}"
                                 src="{{ (config('settings.logo') ? '/storage/img/logo/' . substr(config('settings.logo'), 0, -4) : '/img/logo/PizzaReader') . '-128.png' }}">
                        @endif
                        <h4 style="margin-top:6px;font-weight:700;text-shadow:0 0 12px black">{{ config('settings.reader_name_long', 'PizzaReader') }}</h4>
                        <p style="font-weight:700;text-shadow:0 0 6px black">Loading</p>
                    </div>
                </div>
            </router-view>
        </main>
    </div>
</body>
</html>
