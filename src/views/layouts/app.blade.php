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
        @auth(config('admin.guard'))
            <nav>
                <ul>
                    @foreach(NickDeKruijk\Admin\Helpers::getAllModules() as $module)
                        <li>
                            <a href="{{ route('admin.index', $module->getAdminConfig()->slug) }}">
                                <i class="{{ $module->getAdminConfig()->icon }}"></i>@lang($module->getAdminConfig()->title)
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
        @if (isset($component))
            @livewire($component)
        @endif
        @livewireScripts
    </body>
</html>
