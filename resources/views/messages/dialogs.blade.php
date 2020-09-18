@extends('templates.default')

@section('content')


    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            @if(count($friends))
                <div class="col-md-12">
                    <form class="form-inline" action="{{ route('dialogs.create') }}">
                        <div class="form-group col-lg-10">
                            <select name="userId" id="userId" class="form-control selectpicker" data-width="100%" data-live-search="true" data-size="10">
                                @foreach($friends as $friend)
                                    <option value="{{ $friend->id }}">
                                        {{ $friend->getName() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-default">Create</button>
                    </form>
                </div>
            @endif
            <div class="col-lg-12 media dialogs-show" id="scrollbar">
                @if(count($dialogs))
                    @foreach($dialogs as $dialog)
                        @if(!empty($dialog->lastMessage()))
                            <div class="col-lg-12 media">
                                @if($dialog->first_user_id != Auth::user()->id)
                                    <a href="{{ route('messages.show', ['chatId' => $dialog->id]) }}" class="pull-left">
                                        <img src="{{ $dialog->users()['first']->getAvatarUrl('small') }}" alt="ava" class="media-object avatar-sm">
                                    </a>
                                    <div class="media-body">
                                        <a href="{{ route('messages.show', ['chatId' => $dialog->id]) }}">
                                            {{ $dialog->users()['first']->getName() }}
                                        </a>
                                        @if($dialog->users()['first']->isOnline())
                                            <span class="online"><i class="glyphicon glyphicon-cd"></i></span>
                                        @endif
                                @else
                                    <a href="{{ route('messages.show', ['chatId' => $dialog->id]) }}" class="pull-left">
                                        <img src="{{ $dialog->users()['second']->getAvatarUrl('small') }}" alt="ava" class="media-object avatar-sm">
                                    </a>
                                    <div class="media-body">
                                        <a href="{{ route('messages.show', ['chatId' => $dialog->id]) }}">
                                            {{ $dialog->users()['second']->getName() }}
                                        </a>
                                        @if($dialog->users()['second']->isOnline())
                                            <span class="online"><i class="glyphicon glyphicon-cd"></i></span>
                                        @endif
                                @endif
                                        <div class="media last-message">
                                            @if($dialog->lastMessage()['sender_id'] == Auth::user()->id)
                                                <a href="{{ route('messages.show', ['chatId' => $dialog->id]) }}" class="pull-left">
                                                    <img src="{{ Auth::user()->getAvatarUrl(25) }}" alt="ava" class="media-object avatar-min">
                                                </a>
                                            @endif
                                            <div class="media-body">
                                                <a href="{{ route('messages.show', ['chatId' => $dialog->id]) }}" class="mes">
                                                    <span class="pull-right">{{ $dialog->lastMessage()->created_at->diffForHumans() }}</span>
                                                    {{ $dialog->lastMessage()['text'] }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <h4>You don't have any dialog yet</h4>
                @endif
            </div>
        </div>
    </div>

@stop

@section('styles')
    <link rel="stylesheet" href="{{ URL::asset('/css/bootstrap-select.css') }}">
@stop

@section('scripts')
    <script src="{{ URL::asset('/js/bootstrap-select.js') }}"></script>
@stop

