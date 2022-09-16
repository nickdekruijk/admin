<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin</title>
        <link rel="stylesheet" href="{{ route('admin.css') }}">
        @livewireStyles
    </head>
    <body class="flex bg-dark">
        @auth(config('admin.guard'))
            <nav class="t-white nowrap bg-dark">
                <div class="logo logo-width lh-h bg-light fs-header fw-300">
                    {!! config('admin.logo') !!}
                </div>
                <ul>
                    @foreach($admin->modules as $item)
                        <li class="{{ $item->getAdminConfig()->slug === $admin->module->getAdminConfig()->slug ? 'active' : '' }} pr-2">
                            <a class="block pr-2 py-2" href="{{ route('admin.index', $item->getAdminConfig()->slug) }}">
                                {!! $item->getAdminConfig()->icon() !!}@lang($item->getAdminConfig()->title)
                            </a>
                        </li>
                    @endforeach
                    <li>
                        <form method="post" action="{{ route('admin.logout') }}" class="pr-2 py-2" onclick="this.submit()">
                            @csrf
                            <i class="icon fa-solid fa-right-from-bracket"></i>@lang('Logout')
                        </form>
                    </li>
                </ul>
            </nav>
        @endif
        <div class="flex f-auto mw-full">
            @livewire($admin->component, ['admin' => $admin])
        </div>
        @livewireScripts
    </body>
</html>
