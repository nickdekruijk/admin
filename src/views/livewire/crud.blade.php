<div class="{{ $editing !== false ? 'editing' : '' }}">
    <header>
        <h2>@lang($module->getAdminConfig()->title)</h2>
        <button wire:click="create"><i class="fa-solid fa-plus"></i>@lang('New')</button>
    </header>
    <section class="listview">
        <table>
            <tr>
                @foreach($listview as $column)
                    <th>@lang($column)</th>
                @endforeach
            </tr>
            @foreach($module->all(array_merge(['id'], $listview)) as $row)
                <tr class="{{ $row->id == $editing ? 'active' : '' }}" wire:click="edit({{ $row->id }})">
                    @foreach($listview as $column) 
                        <td>{{ $row->$column }}</td>
                    @endforeach
                </tr>
            @endforeach
        </table>
    </section>
    <section class="editor">
        <div class="buttons">
            <button wire:click="close"><i class="fa-solid fa-ban"></i>@lang('Close')</button>
            @if ($editing)
                <span class="id">{{ $editing }}</id>
            @endif
        </div>
    </section>
</div>
