<div class="w-full bg-grey-light">
    <header class="bg-dark t-white lh-h fs-header fw-300 px-3">
        @lang($this->getAdminConfig()->title)
    </header>
    <div class="p-3 scroll">
        <h2 class="fs-header fw-300">{{ $this->greeting }}</h2>
        <ul class="flex flex-wrap pt-3 -m-3">
            @foreach($modules as $item)
                @if ($item->getAdminConfig()->slug !== $this->getAdminConfig()->slug)
                    <li class="p-3 f-auto">
                        <a class="button button-big" href="{{ route('admin.index', $item->getAdminConfig()->slug) }}">
                            <i class="{{ $item->getAdminConfig()->icon }}"></i>@lang($item->getAdminConfig()->title)
                        </a>
                    </li>
                @endif
            @endforeach
            <li class="p-3 f-auto">
                <form class="button button-big" method="post" action="{{ route('admin.logout') }}" onclick="this.submit()">
                    @csrf
                    <i class="fa-solid fa-right-from-bracket"></i>@lang('Logout')
                </form>
            </li>
        </ul>
    </div>
</div>
