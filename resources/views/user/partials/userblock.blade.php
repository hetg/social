<div class="media">
    <a href="{{ route('profile.index', ['user_id' => $user->id]) }}" class="pull-left">
        <img src="{{ $user->getAvatarUrl('small') }}" alt="ava" class="media-object avatar-sm">
    </a>
    <div class="media-body">
        <h4 class="media-heading">
            <a href="{{ route('profile.index', ['user_id' => $user->id]) }}">{{ $user->getName() }}</a>
            @if($user->isOnline())
                <span class="online"><i class="glyphicon glyphicon-cd"></i></span>
            @endif
        </h4>
        @if($user->location)
            <p>{{ $user->location }}</p>
        @endif
    </div>
</div>