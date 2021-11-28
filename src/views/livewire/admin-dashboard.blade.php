@extends('admin::layouts.app')

@section('component')
    <div>
        <h2>@lang('Welcome back') {{ Auth::user()->name }}.</h2>
    </div>
@endsection
