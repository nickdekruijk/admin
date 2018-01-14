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
            <ul>
                @foreach ($user['nav'] as $id => $item)
                <li class="{{ $id == $slug ? 'active' : '' }}"><a href="{{ url(config('larapages.adminpath') . '/' . str_slug($id)) }}"><i class="fa {{ $item['icon'] }}"></i>{{ isset($item['title'])?$item['title']:ucfirst($id) }}</a></li>
                @endforeach
                <li><form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i>{{ trans('larapages::base.logout') }}</a></li>
            </ul>
        </nav>
        <header>
            <label class="nav-hamburger" for="nav-toggle"><span></span><span></span><span></span></label>
            @yield('header')
            <h2>{{ $user['nav'][$slug]['title'] }}</h2>
        </header>
@yield('view')
        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/nestedSortable/2.0.0/jquery.mjs.nestedSortable.min.js"></script>
        <script src="{{ asset(config('larapages.adminpath') . '/all.js') }}"></script>
    </body>
</html>