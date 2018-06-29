@extends('admin::base')

@section('view')
        <section class="fullpage">
            <h2>{{ trans('admin::base.welcome_back') }} {{ Auth::user()->name }}</h2>
            <div class="dashboard">
                {!! $lp->navigation() !!}
            </div>
        </section>
@endsection
