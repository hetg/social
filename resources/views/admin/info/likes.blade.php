@extends('templates.default')

@section('content')
    <div class="col-lg-offset-2 row">
        <div class="col-lg-8">
            <h2>@lang('admin.user.likes', ['name' => $user->getName()]) <a href="{{ route('admin.info', ['userId' => $user->id]) }}" class="btn btn-default pull-right">@lang('common.back')</a></h2>
            <hr>
            @if(!$likes->count())
                <p>{{ $user->getName() }} hasn't posted anything yet.</p>
            @else
                @foreach($likeable as $liked)
                    <div class="media">
                        <a href="{{ route('profile.index', ['user_id' => $liked->user->id]) }}" class="pull-left">
                            <img src="{{ $liked->user->getAvatarUrl('small') }}" alt="ava" class="media-object avatar-sm">
                        </a>
                        <div class="media-body">
                            <h4 class="media-heading">
                                <a href="{{ route('profile.index', ['user_id' => $liked->user->id]) }}">{{ $liked->user->getName() }}</a>
                                @if($liked->user->isOnline())
                                    <span class="online"><i class="glyphicon glyphicon-cd"></i></span>
                                @endif
                            </h4>
                            <p>{{ $liked->body }}</p>

                            @if(count($liked->attachments()->get()))
                                <div id="{{ "gallery_".$liked->id }}">
                                    @foreach($liked->attachments()->get() as $attachment)
                                        <a href="{{ $attachment->getUrl() }}" data-source="{{ $attachment->getUrl() }}">
                                            <img class="attachment-status" src="{{ $attachment->getUrl() }}">
                                        </a>
                                    @endforeach
                                </div>
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        addGallery('{{ "gallery_$liked->id" }}');
                                    });
                                </script>
                            @endif

                            <ul class="list-inline">
                                <li>{{ $liked->created_at->diffForHumans() }}</li>
                                <li>
                                    <a href="#">
                                        <i class="glyphicon glyphicon-heart-empty"></i>
                                    </a>
                                    {{ $liked->likes()->count() }}
                                </li>
                            </ul>

                            @foreach($liked->replies->sortByDesc('created_at') as $reply)
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
                                        </h5>
                                        <p>{{ $reply->body }}</p>
                                        <ul class="list-inline">
                                            <li>{{ $reply->created_at->diffForHumans() }}</li>
                                            <li>
                                                <a href="#">
                                                    <i class="glyphicon glyphicon-heart-empty"></i>
                                                </a>
                                                {{ $reply->likes()->count() }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                {!! $likes->render() !!}
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