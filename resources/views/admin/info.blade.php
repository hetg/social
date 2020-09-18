@extends('templates.default')

@section('content')
    <div class="col-lg-offset-2 row">
        <div class="col-lg-8">
            <h2>{{ $user->getName() }} <a href="{{ route('admin.index') }}" class="btn btn-default pull-right">@lang('common.back')</a></h2>
            <div class="list-group">
                @foreach($infos as $key => $info)
                    <a href="{{ route('admin.info.'.$key, ['userId' => $user->id]) }}" class="list-group-item">
                        <span class="badge">{{ $info }}</span>
                        @lang('admin.'.$key)
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@stop