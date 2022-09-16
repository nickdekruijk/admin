<div class="flex flex-center t-white relative mw-full p-2">
    <div class="bg-normal rounded mw-full">
        <header class="bg-light fs-header py-1 px-3 logo fw-300">
            {!! config('admin.logo') !!}
        </header>
        <form class="form p-3" wire:submit.prevent="submit">
            @foreach(config('admin.credentials') as $column)
                <label class="block pb-2">
                    @lang(ucfirst($column == 'email' ? 'e-mail address' : $column))
                    <input class="input" size="30" type="{{ $column=='password' ? 'password' : 'text' }}" wire:model.defer="{{ $column }}" {{ $loop->iteration==1 ? 'autofocus' : '' }}>
                </label>
            @endforeach
            @if ($errors->any())
                <div class="t-red fw-700 pb-1">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <button class="input button" type="submit" wire:click="submit"><i class="icon fa-solid fa-right-to-bracket"></i>@lang('Login')</button>
        </form>
    </div>
</div>
