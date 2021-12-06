<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin</title>
        <link rel="stylesheet" href="{{ route('admin.css') }}">
        @livewireStyles
    </head>
    <body class="app">
        @auth(config('admin.guard'))
            <nav>
                <div class="logo">
                    {!! config('admin.logo') !!}
                </div>
                <ul>
                    @foreach(NickDeKruijk\Admin\Helpers::getAllModules() as $item)
                        <li class="{{ $item->getAdminConfig()->slug===$slug ? 'active' : '' }}">
                            <a href="{{ route('admin.index', $item->getAdminConfig()->slug) }}">
                                <i class="{{ $item->getAdminConfig()->icon }}"></i>@lang($item->getAdminConfig()->title)
                            </a>
                        </li>
                    @endforeach
                    <li>
                        <form method="post" action="{{ route('admin.logout') }}" onclick="this.submit()">
                            @csrf
                            <i class="fa-solid fa-right-from-bracket"></i>@lang('Logout')
                        </form>
                    </li>
                </ul>
            </nav>
        @endif
        {{ $slot ?? '' }}
        @if (isset($module))
            @livewire($module->getAdminConfig()->component)
        @endif
        @livewireScripts
    </body>
</html>
