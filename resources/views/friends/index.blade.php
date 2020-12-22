@extends('templates.default')

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <h3>Your friends</h3>

            @if(!$friends->count())
                <p>You have no friends.</p>
            @else
                @foreach($friends as $user)
                    @include('user.partials.userblock')
                    <div class="controls">
                        <form action="{{ route('friends.delete', ['user_id' => $user->id]) }}" method="post">
                            <input type="submit" value="Delete friend" class="btn btn-primary">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </form>
                    </div>
                @endforeach
            @endif
        </div>
        <div class="col-lg-6">
            <h4>Friend requests</h4>

            @if(!$requests->count())
                <p>You have no friend requests.</p>
            @else
                @foreach($requests as $user)
                    @include('user.partials.userblock')<br>
                    <a href="{{ route('friends.accept', ['user_id' => $user->id]) }}" class="btn btn-primary">Accept friend request</a>
                @endforeach
            @endif
        </div>
    </div>
@stop
