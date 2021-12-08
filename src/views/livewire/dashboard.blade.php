<div>
    <header>
        <h2>@lang($this->getAdminConfig()->title)</h2>
    </header>
    <div class="module-content">
        <h2>{{ $this->greeting }}</h2>
        <ul class="dashboard-buttons">
            @foreach(NickDeKruijk\Admin\Helpers::getAllModules() as $item)
                @if ($item->getAdminConfig()->slug !== $this->getAdminConfig()->slug)
                    <li>
                        <a href="{{ route('admin.index', $item->getAdminConfig()->slug) }}">
                            <i class="{{ $item->getAdminConfig()->icon }}"></i>@lang($item->getAdminConfig()->title)
                        </a>
                    </li>
                @endif
            @endforeach
            <li>
                <form method="post" action="{{ route('admin.logout') }}" onclick="this.submit()">
                    @csrf
                    <i class="fa-solid fa-right-from-bracket"></i>@lang('Logout')
                </form>
            </li>
        </ul>
    </div>
</div>
