<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle }}</title>

    <!-- Styles -->
    <link href="/css/jquerysctipttop.css" rel="stylesheet">
    <link href="/css/timingfield.css" rel="stylesheet">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/animate.css" rel="stylesheet">
    <link href="/css/jquery.datetimepicker.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/bower_components/semantic/dist/semantic.min.css"/>
    <link rel="stylesheet" href="/bower_components/jquery-duration-picker/dist/jquery-duration-picker.min.css"/>
    <link rel="stylesheet" href="/css/simplemde.min.css">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css">
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,300,400,600" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- Custom Styling -->
    <link href="/css/styles.css" rel="stylesheet">

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

{{--Scripts--}}
<script src="/js/jquery-3.1.1.min.js"></script>
<script src="/js/jquery.waypoints.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/jquery.datetimepicker.full.min.js"></script>
<script src="/js/jquery-ui.min.js"></script>
<script src="/js/bootstrap3-typeahead.min.js"></script>

{{--Include files for code editor--}}
<script src="/modules/code-editor/ace.js" type="text/javascript" charset="utf-8"></script>

{{--Include files for Markdown Editor--}}
<script src="/js/simplemde.min.js"></script>
{{--Include for Markdown preview--}}
<script src="/js/marked.min.js"></script>

{{--Files of duration picker--}}
<script src="/js/timingfield.js"></script>

{{--Moment Library for client side date time processing--}}
<script src="/js/moment.js"></script>

<script src="/js/script.js"></script>

</body>
</html>

