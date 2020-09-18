@extends('templates.default')

@section('content')
    <div class="row">
        <div class="col-lg-5">
            @include('profile.userblock')
            <hr>
            @if(Auth::user()->id == $user->id)
                <form action="{{ route('status.post') }}" method="post" role="form" enctype="multipart/form-data">
                    <div class="form-group{{ $errors->has('status') ? ' has-error' : ''}}">
                        <div class="input-group">
                            <textarea placeholder="What's up {{ Auth::user()->getFirstName() }}?" name="status" class="form-control" rows="2" aria-describedby="attach-addon"></textarea>
                            <label for="attachments" class="input-group-addon" id="attach-addon"><i class="glyphicon glyphicon-plus"></i></label>
                        </div>
                        <input type="file" name="attachments[]" id="attachments" multiple class="hidden" accept="image/jpeg,image/png">
                        <div id="attachments-container">

                        </div>
                        @if( $errors->has('status') )
                            <span class="help-block">{{ $errors->first('status') }}</span>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-default">Update status</button>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
                <br>
            @endif

            @if(!$statuses->count())
                @if(Auth::user()->id != $user->id)
                    <p>{{ $user->getName() }} hasn't posted anything yet.</p>
                @else
                    <p>You hasn't posted anything yet.</p>
                @endif
            @else
                @foreach($statuses as $status)
                    <div class="media">
                        <a href="{{ route('profile.index', ['user_id' => $status->user->id]) }}" class="pull-left">
                            <img src="{{ $status->user->getAvatarUrl('small') }}" alt="ava" class="media-object avatar-sm">
                        </a>
                        <div class="media-body">
                            <h4 class="media-heading">
                                <a href="{{ route('profile.index', ['user_id' => $status->user->id]) }}">{{ $status->user->getName() }}</a>
                                @if($status->user->isOnline())
                                    <span class="online"><i class="glyphicon glyphicon-cd"></i></span>
                                @endif
                                @if($status->user->id == Auth::user()->id)
                                    <a href="{{ route('status.delete',['statusId' => $status->id]) }}" class="glyphicon glyphicon-remove pull-right"></a>
                                @endif
                            </h4>
                            <p>{{ $status->body }}</p>

                            @if(count($status->attachments()->get()))
                                <div id="{{ "gallery_".$status->id }}">
                                    @foreach($status->attachments()->get() as $attachment)
                                        <a href="{{ $attachment->getUrl() }}" data-source="{{ $attachment->getUrl() }}">
                                            <img class="attachment-status" src="{{ $attachment->getUrl() }}">
                                        </a>
                                    @endforeach
                                </div>
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        addGallery('{{ "gallery_$status->id" }}');
                                    });
                                </script>
                            @endif

                            <ul class="list-inline">
                                <li>{{ $status->created_at->diffForHumans() }}</li>
                                @if(!Auth::user()->hasLikedStatus($status))
                                    <li>
                                        <a href="{{ route('status.like',['statusId' => $status->id]) }}">
                                            <i class="glyphicon glyphicon-heart-empty"></i>
                                        </a>
                                        {{ $status->likes()->count() }}
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ route('status.unlike',['statusId' => $status->id]) }}">
                                            <i class="glyphicon glyphicon-heart"></i>
                                        </a>
                                        {{ $status->likes()->count() }}
                                    </li>
                                @endif
                            </ul>

                            @foreach($status->replies->sortByDesc('created_at') as $reply)
                                <div class="media">
                                    <a href="{{ route('profile.index', ['user_id' => $reply->user->id]) }}" class="pull-left">
                                        <img src="{{ $reply->user->getAvatarUrl('small') }}" alt="ava" class="media-object avatar-sm">
                                    </a>
                                    <div class="media-body">
                                        <h5 class="media-heading">
                                            <a href="{{ route('profile.index', ['user_id' => $reply->user->id]) }}">{{ $reply->user->getName() }}</a>
                                            @if($reply->user->isOnline())
                                                <span class="online"><i class="glyphicon glyphicon-cd"></i></span>
                                            @endif
                                            @if($reply->user->id == Auth::user()->id)
                                                <a href="{{ route('status.delete',['statusId' => $reply->id]) }}" class="glyphicon glyphicon-remove pull-right"></a>
                                            @endif
                                        </h5>
                                        <p>{{ $reply->body }}</p>
                                        <ul class="list-inline">
                                            <li>{{ $reply->created_at->diffForHumans() }}</li>
                                            @if(!Auth::user()->hasLikedStatus($reply))
                                                <li>
                                                    <a href="{{ route('status.like',['statusId' => $reply->id]) }}">
                                                        <i class="glyphicon glyphicon-heart-empty"></i>
                                                    </a>
                                                    {{ $reply->likes()->count() }}
                                                </li>
                                            @else
                                                <li>
                                                    <a href="{{ route('status.unlike',['statusId' => $reply->id]) }}">
                                                        <i class="glyphicon glyphicon-heart"></i>
                                                    </a>
                                                    {{ $reply->likes()->count() }}
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            @endforeach

                            @if($authUserIsFriend || Auth::user()->id === $status->user->id)
                            <form action="{{ route('status.reply',['statusId' => $status->id]) }}" method="post" role="form">
                                <div class="form-group{{ $errors->has("reply-{$status->id}") ? ' has-error' : ''}}">
                                    <textarea placeholder="Reply to this status." name="reply-{{ $status->id }}" class="form-control" rows="2"></textarea>
                                    @if( $errors->has("reply-{$status->id}") )
                                        <span class="help-block">{{ $errors->first("reply-{$status->id}") }}</span>
                                    @endif
                                </div>
                                <input type="submit" value="Reply" class="btn btn-default btn-sm">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </form>
                            @endif
                        </div>
                    </div>
                @endforeach

                {!! $statuses->render() !!}
            @endif
        </div>
        <div class="col-lg-4 col-lg-offset-3">
            @if(Auth::user()->id !== $user->id)
                <h4>{{ $user->getFirstName() }}'s friends</h4>
            @else
                <h4>Your friends</h4>
            @endif

            @if(!$user->friends()->count())
                @if(Auth::user()->id !== $user->id)
                    <p>User {{ $user->getName() }} has no friends.</p>
                @else
                    <p>You have no friends.</p>
                @endif
            @else
                @foreach($user->friends() as $user)
                    @include('user.partials.userblock')
                @endforeach
            @endif

        </div>

    </div>
@stop

@section('styles')
    <link rel="stylesheet" href="{{  URL::asset('/css/gallery.css') }}">
@stop

@section('scripts')

    <script src="{{ URL::asset('/js/gallery.min.js') }}"></script>
    <script type="text/javascript">
        function readURL(input) {

            if (input.files && input.files[0]) {
                $.each(input.files ,function (index, file) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('#attachments-container').append("<img src='" + e.target.result + "' class='attachment-sm'>");
                    };

                    reader.readAsDataURL(file);
                });

            }
        }

        $("#attachments").change(function() {
            readURL(this);
        });

        addGallery("avatar");

        function addGallery(id) {
            $('#'+id).magnificPopup({
                delegate: 'a',
                type: 'image',
                closeOnContentClick: false,
                closeBtnInside: false,
                mainClass: 'mfp-with-zoom mfp-img-mobile',
                image: {
                    verticalFit: true,
                    titleSrc: function(item) {
                        return '<a class="image-source-link" href="'+item.el.attr('data-source')+'" target="_blank">Open original</a>';
                    }
                },
                gallery: {
                    enabled: true
                },
                zoom: {
                    enabled: true,
                    duration: 300, // don't foget to change the duration also in CSS
                    opener: function(element) {
                        return element.find('img');
                    }
                }

            });
        }


    </script>

@stop