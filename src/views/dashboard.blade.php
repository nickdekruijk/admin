@extends('larapages::base')

@section('view')
        <section class="fullpage">
            <h2>{{ trans('larapages::base.welcome_back') }} {{ Auth::user()->name }}</h2>
            <div class="dashboard">
                {!! $lp->navigation() !!}
            </div>
        </section>
@endsection
