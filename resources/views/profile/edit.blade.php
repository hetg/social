@extends('templates.default')

@section('content')
    <div class="row">
        <div class="col-lg-10 col-lg-offset-2 ">
            <div class="col-lg-6">
                <h3>Edit profile</h3>
                <form name="info" role="form" method="post" action="{{ route('profile.editPost') }}" class="form-vertical">
                    <div class="form-group{{ $errors->has('first_name') ? ' has-error' : ''}} col-lg-6">
                        <label for="first_name" class="control-label">First name</label>
                        <input type="text" name="first_name" class="form-control" id="first_name" value="{{ Request::old('first_name') ?: Auth::user()->first_name }}">
                        @if( $errors->has('first_name') )
                            <span class="help-block">{{ $errors->first('first_name') }}</span>
                        @endif
                    </div>
                    <div class="form-group{{ $errors->has('last_name') ? ' has-error' : ''}} col-lg-6">
                        <label for="last_name" class="control-label">Last name</label>
                        <input type="text" name="last_name" class="form-control" id="last_name" value="{{ Request::old('last_name') ?: Auth::user()->last_name }}">
                        @if( $errors->has('last_name') )
                            <span class="help-block">{{ $errors->first('last_name') }}</span>
                        @endif
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="location" class="control-label">Location</label>
                            <input type="text" name="location" class="form-control" id="location" value="{{ Request::old('location') ?: Auth::user()->location }}">
                        </div>
                        <div class="form-group">
                            <input class="btn btn-default" type="submit" value="Update">
                        </div>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div>

            <div class="col-lg-2">
                <h3>Edit avatar</h3>
                <form enctype="multipart/form-data" name="info" role="form" method="post" action="{{ route('profile.editAvatar') }}" class="form-vertical">
                    <div class="form-group">
                        <label for="avatar" class="control-label"><img id="avatar-prev" class="avatar-lg" src="{{ Auth::user()->getAvatarUrl() }}" alt=""></label>
                        <input type="file" name="avatar" class="form-control hidden" id="avatar" accept="image/jpeg,image/png">
                    </div>
                    <div class="form-group">
                        <input class="btn btn-default" type="submit" value="Update">
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div>
        </div>

        <div class="col-lg-offset-2 col-lg-7">
            <h3>Change password</h3>
            <form name="pass" role="form" method="post" action="{{ route('profile.editPass') }}" class="form-vertical">
                <div class="form-group{{ $errors->has('old_password') ? ' has-error' : ''}} col-lg-12">
                    <label for="old_password" class="control-label">Old password</label>
                    <input type="password" name="old_password" class="form-control" id="old_password" value="">
                    @if( $errors->has('old_password') )
                        <span class="help-block">{{ $errors->first('old_password') }}</span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : ''}} col-lg-6">
                    <label for="password" class="control-label">New password</label>
                    <input type="password" name="password" class="form-control" id="password" value="">
                    @if( $errors->has('password') )
                        <span class="help-block">{{ $errors->first('password') }}</span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : ''}} col-lg-6">
                    <label for="password_confirmation" class="control-label">Password confirmation</label>
                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" value="">
                    @if( $errors->has('password_confirmation') )
                        <span class="help-block">{{ $errors->first('password_confirmation') }}</span>
                    @endif
                </div>
                <div class="form-group col-lg-12">
                    <button class="btn btn-default" type="submit">Change</button>
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </form>
        </div>
    </div>
@stop

@section('scripts')

    <script type="text/javascript">
        function readURL(input) {

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#avatar-prev').attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#avatar").change(function() {
            readURL(this);
        });
    </script>

@stop