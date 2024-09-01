<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Maintenance | {{ config('settings.reader_name') }}</title>

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
<body class="dark" data-bs-theme="dark">
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
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                </div>
            </div>
        </nav>

        <main class="container-lg p-0 py-sm-4 overflow-hidden">
            <div class="text-center">
                <h1>The reader is currently in maintenance.</h1>
                <h2>Please retry later.</h2>
                <img src="{{ asset('img/404.gif') }}" />
            </div>
        </main>
    </div>
</body>
</html>
