@extends('layout')

@section('title', '| New Sales manager')

@section('body')
    <form action="../../../NewPasswordManager" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="SecurityKey1" value="{{$SecurityKey1}}">
        <input type="hidden" name="SecurityKey2" value="{{$SecurityKey2}}">
        <input type="password" name="password" placeholder="Password">
        {{ $errors->first('password') }}
        <input type="password" name="password_confirmation" placeholder="Confirm password">
        {{ $errors->first('password_confirmation') }}
        <input type="submit">
    </form>
@endsection