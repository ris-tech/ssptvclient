<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
  
    <title>{{ config('app.name', 'SSP TV Client') }}</title>
  
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
  
    <!-- Scripts -->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap-icons.min.css')}}" />

    <script src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>
    <script>
        window.tvLocationId = '{{$tvs->location_id}}';
        window.tvLocationName = '{{$tvs->location->city}}';
        window.getWeatherData = '{{route("tv.getWeatherData")}}';
        window.csrf_token = '{{csrf_token() }}';
        window.weatherIconPath = "{{asset('assets/img/weather')}}";
        window.getNewData = '{{route("getNewData")}}';
        window.getCurrentSlide = '{{route("getCurrentSlide")}}';
        window.slideIds = '{{$slideIds}}';
        window.getUpdatedData = '{{route("getUpdatedData")}}';
        window.imgPath = "{{asset('assets/img')}}";
    </script>
    <link rel="stylesheet" href="{{asset('assets/css/view.css')}}" />
</head>
<body class="m-0 p-0">
    <div class="updateBar position-absolute m-3 p-2 bg-success text-light" style="z-index:50000; right:10px;">
        Ажурирано
    </div>
    <div class="onlinebar position-absolute m-0 p-0 bg-success w-100" style="z-index:50000;height:3px;">
        
    </div>
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

