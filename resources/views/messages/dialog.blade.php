@extends('templates.default')

@section('content')


    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <div class="main-block">
                <div class="settings-block">
                    <p style="font-weight: bold; text-indent: 10px;">
                        {{ $user->getName() }}
                        @if($user->isOnline())
                            <span class="online"><i class="glyphicon glyphicon-cd"></i></span>
                        @endif
                    </p>
                </div>
                <div class="display-block" id="scrollbar">
                    @foreach($messages as $message)
                        @if(Auth::user()->id == $message->sender_id)
                            <div class="col-lg-12">
                                <div class="message text-right">
                                    <h5>
                                        <a href="{{ route('profile.index', ['user_id' => Auth::user()->id]) }}">
                                            {{ Auth::user()->getFirstName() }}
                                        </a>
                                    </h5>
                                    <span class="pull-left">{{ $message->created_at->diffForHumans() }}</span>
                                    <p>{{ $message->text }}</p>
                                </div>
                            </div>
                        @else
                            <div class="col-lg-12">
                                <div class="message text-left">
                                    <h5>
                                        <a href="{{ route('profile.index', ['user_id' => $message->sender_id]) }}">
                                            {{ $user->getFirstName() }}
                                        </a>
                                    </h5>
                                    <span class="pull-right">{{ $message->created_at->diffForHumans() }}</span>
                                    <p>{{ $message->text }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <hr>
                <div class="send-block">
                    <form action="{{ route('messages.send', ['chatId' => $chat_id]) }}" method="post" role="form">
                        <div class="form-group{{ $errors->has('message') ? ' has-error' : ''}}">
                            <textarea placeholder="Type here..." name="message" class="form-control" rows="2"></textarea>
                            @if( $errors->has('message') )
                                <span class="help-block">{{ $errors->first('message') }}</span>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-default">Send</button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop

@section('scripts')

    <script type="text/javascript">
        $("#scrollbar").scrollTo('max');
    </script>

@stop
