<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PizzaReader') }}</title>

    <!-- Scripts -->
    <script type="text/javascript">
        const API_BASE_URL = "{{ config('app.url') . 'api'}}" /* TODO setting api */
        function timePassed(date){
            // TODO check timezone
            let diff = new Date().getTime() - new Date(date).getTime();
            diff = parseInt(diff/1000);
            if(diff < 60) return diff + " secs ago";
            diff = parseInt(diff/60);
            if(diff === 1) return diff + " min ago";
            if(diff < 60) return diff + " mins ago";
            diff = parseInt(diff/60);
            if(diff === 1) return diff + " hour ago";
            if(diff < 24) return diff + " hours ago";
            diff = parseInt(diff/24);
            if(diff === 1) return diff + " day ago";
            if(diff < 30) return diff + " days ago";
            diff = parseInt(diff/30);
            if(diff === 1) return diff + " month ago";
            if(diff < 12) return diff + " months ago";
            diff = parseInt(diff/12);
            if(diff === 1) return diff + " year ago";
            return diff + " years ago";
        }

        window.timePassed = timePassed;
    </script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/card-search.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/reader.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'PizzaReader') }}
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
                            <router-link to="/comics" class="nav-link">Comics</router-link>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        @include('partials.card-search')
                    </ul>
                </div>
            </div>
        </nav>
        <main class="container-lg p-0 py-sm-4 overflow-hidden">
            <router-view></router-view>
        </main>
    </div>
</body>
</html>
