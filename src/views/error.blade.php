@extends('larapages::base')

@section('view')
        <section class="fullpage">
            <h2>{{ trans('larapages::base.somethingwrong') }}</h2>
            <p>{{ $message }}</p>
        </section>
@endsection
