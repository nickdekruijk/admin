<div class="login">
    <div class="popup">
        <header>
            <h2 class="logo">{!! config('admin.logo') !!}</h2>
        </header>
        <form class="form" wire:submit.prevent="submit">
            @foreach(config('admin.credentials') as $column)
                <label>
                    @lang(ucfirst($column == 'email' ? 'e-mail address' : $column))
                    <input required type="{{ $column=='password' ? 'password' : 'text' }}" wire:model.defer="{{ $column }}" {{ $loop->iteration==1 ? 'autofocus' : '' }}>
                </label>
            @endforeach
            @if ($errors->any())
                <div class="error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <button type="submit" wire:click="submit"><i class="fa-solid fa-right-to-bracket"></i>@lang('Login')</button>
        </form>
    </div>
</div>
