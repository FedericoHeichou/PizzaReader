<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php
    use App\Http\Controllers\Reader\ReaderController;
    use App\Models\Comic;
    use App\Models\Chapter;

    $comic_slug = request()->segment(2);
    $comic_title = "";
    $pre = "";
    $meta_description = config('settings.description');
    $meta_image = config('settings.cover_path');

    $user_agent = strtolower(request()->userAgent());

    if((strpos($user_agent, 'bot') !== false || strpos($user_agent, 'facebook') !== false) && $comic_slug){
        $comic = Comic::publicSlug($comic_slug);
        if($comic) {
            $comic_title = $comic->name;
            $meta_description = $comic->description;
            $meta_image = 'storage/' . Comic::buildPath($comic) . '/' . urlencode($comic->thumbnail);
            if(request()->segment(1) === "read") {
                $ch_uri = substr(request()->getRequestUri(), strlen(request()->segment(1) . "/" . request()->segment(2) . "/" . request()->segment(3)) + 2);
                $ch = ReaderController::explodeCh(request()->segment(3), $ch_uri);
                $chapter = Chapter::publicFilterByCh($comic, $ch);
                if($chapter) {
                    $pre = Chapter::getVolChSub($chapter) . " | ";
                    $meta_image = 'storage/' . Chapter::buildPath($comic, $chapter) . '/' . $chapter->pages()->first()->filename;
                }
            }
        }
    } else {
        $comic_title = ucwords(str_replace("-", " ", $comic_slug));
        if(request()->segment(1) === "read") {
            $ch_uri = substr(request()->getRequestUri(), strlen(request()->segment(1) . "/" . request()->segment(2) . "/" . request()->segment(3)) + 2);
            $ch = ReaderController::explodeCh(request()->segment(3), $ch_uri);
            $chapter = new Chapter();
            $chapter->volume = $ch['vol'];
            $chapter->chapter = $ch['ch'];
            $chapter->subhapter = $ch['sub'];
            $pre = Chapter::getVolChSub($chapter) . " | ";
        }
    }
    $title = $pre . ($comic_title ? $comic_title . " | " . config('settings.reader_name') : config('settings.reader_name_long'));
?>
    <title>{{ $title }}</title>

    <!-- SEO -->
    <link rel="canonical" href="{{ URL::current() }}" />
    <meta name="description" content="{{ $meta_description }}"/>
    <meta property="og:image" content="{{ asset($meta_image) }}" />
    <meta property="og:image:secure_url" content="{{ asset($meta_image) }}" />
    <?php $size = getimagesize($meta_image); ?><meta property="og:image:width" content="{{ $size[0] ?? 0 }}" />
    <meta property="og:image:height" content="{{ $size[1] ?? 0 }}" />
    <meta property="og:site_name" content="{{ config('settings.reader_name') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $title }}" />
    <meta property="og:description" content="{{ $meta_description }}" />
    <meta property="og:url" content="{{ URL::current() }}" />

    <!-- Scripts -->
    <script type="text/javascript">
        const BASE_URL = "{{ substr(config('app.url'), -1) === '/' ? config('app.url') : config('app.url') . '/' }}"
        const API_BASE_URL = BASE_URL + 'api';
        const SITE_NAME = "{{ config('settings.reader_name') }}";
        const SITE_NAME_FULL = "{{ config('settings.reader_name_long') }}";
    </script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/card-search.js') }}" defer></script>
    <script src="{{ asset('js/frontend.js') }}" defer></script>
    <script src="{{ asset('js/jquery.touchSwipe.min.js') }}" defer></script>
    <script src="{{ asset('js/reader.js') }}" defer></script>
    <script src="{{ asset('js/dark.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/frontend.css') }}" rel="stylesheet">
    <link href="{{ asset('css/reader.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dark.css') }}" rel="stylesheet">

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
        <nav id="header" class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container px-lg-0">
                <a class="navbar-brand" href="{{ config('settings.home_link', url('/')) }}">
                    @if(config('settings.logo'))
                        <img alt="Logo of {{ config('settings.reader_name', 'PizzaReader') }}"
                             title="Logo of {{ config('settings.reader_name', 'PizzaReader') }}"
                             class="logo" src="{{ config('settings.logo_asset_72') }}">
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
                            <router-link to="/" class="nav-link">
                                <span aria-hidden="true" title="Last Releases" class="fas fa-list-alt fa-fw"></span> Last Releases
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/recommended" class="nav-link">
                                <span aria-hidden="true" title="Recommended" class="fas fa-star fa-fw"></span> Recommended
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/comics" class="nav-link">
                                <span aria-hidden="true" title="Comics" class="fas fa-book fa-fw"></span> All Comics
                            </router-link>
                        </li>
                    @foreach(parse_ini_string(config('settings.menu')) as $k => $v)
                        <?php
                            $a = explode(",", $v);
                            $css = count($a) ? $a[0] : null;
                            $url = count($a) > 1 ? $a[1] : null;
                        ?>
                        @if($url !== null && $css !== null)
                        <li class="nav-item">
                            <a href="{{ $url }}" class="nav-link" target="_blank">
                                @if($css)<span aria-hidden="true" title="Comics" class="{{ $css }}"></span>@endif {{ $k }}
                            </a>
                        </li>
                        @endif
                    @endforeach
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <div class="custom-control custom-switch p-2 ml-4">
                                <input type="checkbox" class="custom-control-input" id="dark-mode-switch"
                                    {{ isset($_COOKIE["dark"]) &&  $_COOKIE["dark"] ? "checked" : "" }}>
                                <label class="custom-control-label" for="dark-mode-switch">Dark</label>
                            </div>
                        </li>
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
    <footer>
        <div class="footer-copyright text-center py-3 text-muted">
            {{ config("settings.footer") }}
        </div>
    </footer>
    <div id="loader" class="lds-ring" style="display: none;"><div></div><div></div><div></div><div></div></div>
    <script>const homepage_html_placeholder = "{!! substr(json_encode(config('settings.homepage_html')), 1, -1) !!}"</script>
</body>
</html>
