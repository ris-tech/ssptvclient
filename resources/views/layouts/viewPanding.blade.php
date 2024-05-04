<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
  
    <title>{{ config('app.name', 'Laravel') }}</title>
  
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
  
    <!-- Scripts -->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}" />
    <script src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap-icons.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/view.css')}}" />
    <style>
        body {
            background-image: url("{{asset('assets/img/bg_tv.jpg')}}");
            background-position: cover;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body class="m-0 p-0">
    <div id="app m-0 p-0">
        <main class="p-0 m-0">
            <div class="container-fluid m-0 p-0">
                @yield('content')
            </div>
        </main>          
    </div>
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/tv.js') }}"></script>
</body>
</html>

