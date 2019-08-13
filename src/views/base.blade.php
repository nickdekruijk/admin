<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{{ asset(config('admin.adminpath') . '/all-css') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body class="{{ $lp->browse() }}">
        <div class="loading"><i class="fa fa-circle-o-notch fa-spin"></i></div>
        <nav class="{{ isset($_COOKIE['nav-toggle']) && $_COOKIE['nav-toggle']=='true'?'expanded':'' }}">
            <h1><a href="{{ url(config('admin.logo_link') ?? config('admin.adminpath')) }}" target="{{ config('admin.logo_link_target') }}">{!! config('admin.logo') !!}</a></h1>
            {!! $lp->navigation() !!}
        </nav>
        <header>
            <label class="nav-hamburger"><span></span><span></span><span></span></label>
            @yield('header')
            <h2>
                {{ $lp->module('title') }}
                @if (request()->root && $lp->module('sub_navigation'))
                ({{$lp->model()->find(request()->root)[$lp->module('sub_navigation')]}})
                @endif
            </h2>
        </header>
@yield('view')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="{{ asset(config('admin.adminpath') . '/all-js') }}"></script>
        @yield('scripts')
    </body>
</html>
