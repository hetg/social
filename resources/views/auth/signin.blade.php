@extends('templates.default')

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <h3>@lang('auth.welcome_title')</h3>
            <p>@lang('auth.welcome_body')</p>
        </div>
        <div class="col-lg-6">
            <h3>@lang('auth.sign_in')</h3>
            <form role="form" method="post" action="{{ route('auth.signin') }}" class="form-vertical">
                <div class="form-group{{ $errors->has('email') ? ' has-error' : ''}}">
                    <label for="email" class="control-label">@lang('auth.email')</label>
                    <input autofocus type="text" name="email" class="form-control" id="email" value="{{ Request::old('email') ?: '' }}">
                    @if( $errors->has('email') )
                        <span class="help-block">{{ $errors->first('email') }}</span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : ''}}">
                    <label for="password" class="control-label">@lang('auth.password')</label>
                    <input type="password" name="password" class="form-control" id="password" value="">
                    @if( $errors->has('password') )
                        <span class="help-block">{{ $errors->first('password') }}</span>
                    @endif
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="remember"> @lang('auth.remember_me')
                    </label>
                </div>
                <div class="form-group">
                    <button class="btn btn-default" type="submit">@lang('auth.sign_in')</button>
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </form>
        </div>
    </div>
@stop