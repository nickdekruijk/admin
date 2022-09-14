<div class="w-full bg-grey-light{{ $editing !== false ? ' editing' : '' }}">
    <header class="bg-dark t-white lh-h fs-header fw-300 px-3">
        @lang($module->getAdminConfig()->title)
        @if (Gate::allows('admin.create', $module))
            <button class="button" wire:click="create"><i class="fa-solid fa-plus"></i>@lang('Add')</button>
        @endif
    </header>
    <section class="scroll">
        <table class="listview">
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
        <div class="bg-normal t-white lh-40 px-2">
            @if ($editing)
                @if (Gate::allows('admin.update', $module))
                    <button class="button" wire:click="update"><i class="fa-solid fa-save"></i>@lang('Save')</button>
                @endif
                @if (Gate::allows('admin.create', $module))
                    <button class="button" wire:click="clone"><i class="fa-solid fa-copy"></i>@lang('Save as copy')</button>
                @endif
                @if (Gate::allows('admin.delete', $module))
                    <button class="button" wire:click="delete"><i class="fa-solid fa-trash"></i>@lang('Delete')</button>
                @endif
            @elseif (Gate::allows('admin.create', $module))      
                <button class="button" wire:click="create"><i class="fa-solid fa-save"></i>@lang('Add')</button>
            @endif
            <button class="button" wire:click="close"><i class="fa-solid fa-ban"></i>@lang('Close')</button>
            @if ($editing)
                <span class="id">{{ $editing }}</id>
            @endif
        </div>
        <div class="columns p-2">
            @foreach($module->getAdminConfig()->getCrudColumns() as $column)
                <label>
                    @lang($column->label)
                    @if ($column->type == 'media')
                        Add media here
                    @else
                        <input class="input" wire:model.lazy="data.{{ $column->column }}" placeholder="{{ __($column->placeholder ?? null) ?: Str::slug($data['title'] ?? null) }}" />
                    @endif
                </label>
            @endforeach
        </div>
    </section>
</div>
