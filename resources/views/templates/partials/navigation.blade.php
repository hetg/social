<nav class="navbar navbar-default" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
                <span class="sr-only">@lang('common.toggle_nav')</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ route('home') }}">Heals</a>
        </div>

        <div class="collapse navbar-collapse" id="navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                @if (Auth::check())
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle notification" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="glyphicon glyphicon-bell"></i>
                        </a>
                        <ul class="dropdown-menu notifications">
                            @if(count(Auth::user()->unreadNotifications))
                                @foreach(array_slice(Auth::user()->unreadNotifications->toArray(), 0, 8) as $notification)
                                    <li>
                                        <a href="{{ route($notification['data']['route'], $notification['data']['params']) }}">
                                            <h4>{{ $notification['data']['title'] }}</h4>
                                            {{ $notification['data']['text'] }}
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                @if(count(Auth::user()->notifications))
                                    @foreach(array_slice(Auth::user()->notifications->toArray(), 0, 8) as $notification)
                                        <li>
                                            <a href="{{ route($notification['data']['route'], $notification['data']['params']) }}">
                                                <h4>{{ $notification['data']['title'] }}</h4>
                                                {{ $notification['data']['text'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                @else
                                    <li>
                                        <a href="#">
                                            <h4>@lang('navigation.no_notifications')</h4>
                                        </a>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    </li>

                    <li{{ Route::currentRouteName() == "home"? " class=active" : ''}}>
                        <a href="{{ route('home') }}"><b>@lang('navigation.feed')</b></a>
                    </li>
                    <li{{ Route::currentRouteName() == "dialogs.show"? " class=active" : ''}}>
                        <a href="{{ route('dialogs.show') }}"><b>@lang('navigation.conversations')</b></a>
                    </li>
                    <li{{ Route::currentRouteName() == "friends.index"? " class=active" : ''}}>
                        <a href="{{ route('friends.index') }}"><b>@lang('navigation.friends')</b></a>
                    </li>

                    <form class="navbar-form navbar-left" role="search" action="{{ route('search.results') }}">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="search-addon"><i class="glyphicon glyphicon-search"></i></span>
                                <input type="text" name="query" class="form-control" placeholder="@lang('navigation.search')" aria-describedby="search-addon">
                            </div>
                        </div>
                    </form>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <img src="{{ Auth::user()->getAvatarUrl('small') }}" alt="ava" class="avatar-mini">
                            <b>{{ Auth::user()->getName() }}</b>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('profile.index', ['user_id' => Auth::user()->id]) }}">@lang('navigation.profile')</a></li>
                            <li><a href="{{ route('profile.edit') }}">@lang('navigation.edit_profile')</a></li>
                            <li role="separator" class="divider"></li>
                            @if(Auth::user()->isAdmin())
                                <li><a href="{{ route('admin.index') }}">@lang('navigation.admin')</a></li>
                                <li role="separator" class="divider"></li>
                            @endif
                            <li><a href="{{ route('auth.signout') }}">@lang('navigation.sign_out')</a></li>
                        </ul>
                    </li>

                @else
                    @if(Route::currentRouteName() != 'auth.signup')
                        <li><a href="{{ route('auth.signup') }}">@lang('navigation.sign_up')</a></li>
                    @else
                        <li><a href="{{ route('home') }}">@lang('navigation.sign_in')</a></li>
                    @endif
                @endif
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>