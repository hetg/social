@extends('templates.default')

@section('content')
    <div class="col-lg-offset-2 row">
        <div class="col-lg-8">
            <h2>@lang('admin.users')</h2>
            <div class="list-group">
                @foreach($users as $user)
                    <p class="list-group-item">
                        {{ $user->getName() }}
                        <a href="{{ route('admin.info', ['userId' => $user->id ]) }}" class="btn-sm btn-success pull-right">@lang('common.open')</a>
                    </p>
                @endforeach
            </div>
        </div>
    </div>
@stop