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
        <input type="checkbox" id="nav-toggle" {{ isset($_COOKIE['nav-toggle']) && $_COOKIE['nav-toggle']=='true'?'checked':'' }}>
        <nav>
            <h1><a href="{{ url(config('larapages.adminpath')) }}">{!! config('larapages.logo') !!}</a></h1>
            {!! $lp->navigation() !!}
        </nav>
        <header>
            <label class="nav-hamburger" for="nav-toggle"><span></span><span></span><span></span></label>
            @yield('header')
            <h2>{{ $lp->module('title') }}</h2>
        </header>
@yield('view')
        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="{{ asset(config('larapages.adminpath') . '/all.js') }}"></script>
        @yield('scripts')
    </body>
</html>