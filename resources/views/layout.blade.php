<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Clothing Store @yield('title')</title>
    @section('style')
    @show
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css">
    <script src="https://js.stripe.com/v3/"></script>
    <script src="js/angular.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</head>
<body ng-app="AppJS">
@section('body')
@show
<footer>
    <a href="https://GitHub.com/RostislavZalevsky">By Rostislav Zalevsky &copy; <?php echo date('Y')?></a>
</footer>
<script>var app = angular.module("AppJS", []);</script>
@section('script')
@show
</body>
</html>
