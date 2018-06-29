@extends('admin::base')

@section('view')
        <section class="fullpage">
            <h2>{{ trans('admin::base.somethingwrong') }}</h2>
            <p>{{ $message }}</p>
        </section>
@endsection
