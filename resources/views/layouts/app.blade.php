<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <!-- Styles -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/styles.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};
    </script>
</head>

<body>
    <div class="space-wrapper">
        @include('layouts.navbar')
        @yield('content')
    </div>

    @include('layouts.footer')

    <!-- Scripts -->
    <script src="/js/jquery-3.1.1.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/script.js"></script>
</body>
</html>
