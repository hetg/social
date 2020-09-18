@extends('templates.default')

@section('content')
    <h2>Oops, that page could not be found.</h2>
    <a href="{{ route('home') }}" class="btn btn-default">Go Home</a>
@stop