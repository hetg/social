@extends('templates.default')

@section('content')
    <div class="col-lg-offset-2 row">
        <div class="col-lg-8">
            <h2>
                @lang('admin.user.messages', ['name' => $user->getName()])
                <a href="{{ route('admin.info', ['userId' => $user->id]) }}" class="btn btn-default pull-right">@lang('common.back')</a>
            </h2>
            <hr>
            @if(!$messages->count())
                <p>{{ $user->getName() }} hasn't posted anything yet.</p>
            @else
                @foreach($messages as $message)
                    <div class="media">
                        <a href="{{ route('profile.index', ['user_id' => $message->sender_id]) }}" class="pull-left">
                            <img src="{{ $user->getAvatarUrl('small') }}" alt="ava" class="media-object avatar-sm">
                        </a>
                        <div class="media-body">
                            <h4 class="media-heading">
                                <a href="{{ route('profile.index', ['user_id' => $message->sender_id]) }}">{{ $user->getName() }}</a>
                                @if($user->isOnline())
                                    <span class="online"><i class="glyphicon glyphicon-cd"></i></span>
                                @endif
                                <a href="{{ route('message.delete',['messageId' => $message->id]) }}" class="glyphicon glyphicon-remove pull-right"></a>
                            </h4>
                            <p>{{ $message->text }}</p>

                            <ul class="list-inline">
                                <li>{{ $message->created_at->diffForHumans() }}</li>
                            </ul>
                        </div>
                    </div>
                @endforeach

                {!! $messages->render() !!}
            @endif
        </div>
    </div>
@stop
