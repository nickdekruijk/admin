<!DOCTYPE html>
<html lang="en">
    <head>
        <title>LaraPages</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{{ asset(config('larapages.adminpath') . '/all.css') }}">
    </head>
    <body>
        <input type="checkbox" id="nav-toggle">
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
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/nestedSortable/2.0.0/jquery.mjs.nestedSortable.min.js"></script>
        <script src="{{ asset(config('larapages.adminpath') . '/all.js') }}"></script>
        @yield('scripts')
    </body>
</html>