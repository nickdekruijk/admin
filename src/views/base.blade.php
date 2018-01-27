<!DOCTYPE html>
<html lang="en">
    <head>
        <title>LaraPages</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{{ asset(config('larapages.adminpath') . '/all.css') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
        <div class="loading"><i class="fa fa-circle-o-notch fa-spin"></i></div>
        <nav class="{{ isset($_COOKIE['nav-toggle']) && $_COOKIE['nav-toggle']=='true'?'expanded':'' }}">
            <h1><a href="{{ url(config('larapages.adminpath')) }}">{!! config('larapages.logo') !!}</a></h1>
            {!! $lp->navigation() !!}
        </nav>
        <header>
            <label class="nav-hamburger"><span></span><span></span><span></span></label>
            @yield('header')
            <h2>{{ $lp->module('title') }}</h2>
        </header>
@yield('view')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="{{ asset(config('larapages.adminpath') . '/all.js') }}"></script>
        @yield('scripts')
    </body>
</html>