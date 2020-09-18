@extends('templates.default')

@section('content')
    <h3>Your search for: <b>"{{ Request::input('query') }}"</b></h3>

    @if(!$users->count())
        <p>No results found, sorry.</p>
    @else
    <div class="row">
        <div class="col-lg-12">
            @foreach($users as $user)
                @include('search.userblock')
            @endforeach
        </div>
    </div>
    @endif
@stop