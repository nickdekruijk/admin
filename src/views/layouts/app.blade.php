<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin</title>
        <link rel="stylesheet" href="{{ route('admin.css') }}">
        @livewireStyles
    </head>
    <body>
        @auth
            <nav>
                {!! NickDeKruijk\Admin\Controllers\AdminController::nav() !!}
            </nav>
        @endif
        {{ $slot ?? '' }}
        @yield('component')
        @livewireScripts
    </body>
</html>
