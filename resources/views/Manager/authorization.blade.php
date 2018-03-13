@extends('layout')
@section('title', '| Authorization')

@section('body')
{{--    {{ \App\Http\Controllers\Controller::Auth() == false ?"asd":"ss" }}--}}
    <form action="Authorization" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="email" name="Email" placeholder="Email">
        <input type="password" name="Password" placeholder="Password">
        <input type="submit">
    </form>
@endsection