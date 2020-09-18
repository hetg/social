<div class="media">
    <div id="avatar">
        <a href="{{ $user->getAvatarUrl() }}" data-source="{{ $user->getAvatarUrl() }}" class="pull-left">
            <img src="{{ $user->getAvatarUrl() }}" alt="ava" class="media-object avatar-lg">
        </a>
    </div>

    <div class="media-body">
        <div class="col-lg-12">
            <h4 class="media-heading">
                <a href="{{ route('profile.index', ['user_id' => $user->id]) }}">{{ $user->getName() }}</a>
                @if($user->isOnline())
                    <span class="online"><i>(Online)</i></span>
                @endif
            </h4>
            @if($user->location)
                <p>{{ $user->location }}</p>
            @endif
        </div>
        <div class="col-lg-12">
            @if(Auth::user()->hasFriendRequestPending($user))
                <p>Waiting for {{ $user->getName() }} to accept your request.</p>
            @elseif(Auth::user()->hasFriendRequestReceived($user))
                <a href="{{ route('friends.accept', ['user_id' => $user->id]) }}" class="btn btn-primary">Accept friend request</a>
            @elseif(Auth::user()->isFriendsWith($user))
                <p>You and {{ $user->getName() }} are friends.</p>


                <form action="{{ route('friends.delete', ['user_id' => $user->id]) }}" method="post">
                    @if($user->dialogs()->where('first_user_id',Auth::user()->id)->first())
                        <a href="{{ route('messages.show', ['chatId' => $user->dialogs()->where('first_user_id',Auth::user()->id)->first()['id']]) }}" class="btn btn-primary">
                            <i class="glyphicon glyphicon-comment"></i>
                        </a>
                    @elseif($user->dialogs()->where('second_user_id',Auth::user()->id)->first())
                        <a href="{{ route('messages.show', ['chatId' => $user->dialogs()->where('second_user_id',Auth::user()->id)->first()['id']]) }}" class="btn btn-primary">
                            <i class="glyphicon glyphicon-comment"></i>
                        </a>
                    @else
                        <a href="{{ route('dialogs.create', ['userId' => $user->id]) }}" class="btn btn-primary">
                            <i class="glyphicon glyphicon-comment"></i>
                        </a>
                    @endif
                    <input type="submit" value="Delete friend" class="btn btn-primary">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            @elseif(Auth::user()->id !== $user->id)
                <a href="{{ route('friends.add', ['user_id' => $user->id]) }}" class="btn btn-primary">Add as friend</a>
            @endif
        </div>
    </div>
</div>