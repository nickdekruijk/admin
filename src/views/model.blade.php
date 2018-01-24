@extends('larapages::base')

@section('header')
    @if ($lp->can('create'))
    <button class="button model_create"><i class="fa fa-plus-circle"></i>{{ $lp->locale('new', $lp->module(), trans('larapages::base.new')) }}</button>
    @endif
@endsection

@section('view')
    <input type="checkbox" id="edit-toggle">
        <section id="listview" class="{{ $lp->module('treeview')?'treeview':'' }}">
            @if ($lp->can('read'))
            <div class="header">
                {!! $lp->listviewIndex() !!}
            </div>
            @endif
            <div class="content{{ $lp->module('sortable')?' sortable':'' }}">
                @if ($lp->can('read'))
                {!! $lp->listviewData() ?: '<ul></ul>' !!}
                @endif
                @if ($lp->can('create'))
                <button class="button add model_create"><i class="fa fa-plus-circle"></i>{{ $lp->locale('new', $lp->module(), trans('larapages::base.new')) }}</button>
                @endif
            </div>
        </section>
        @if ($lp->can('read'))
        <section id="editview">
            <form id="model_form">
            <div class="header">
                @if ($lp->can('update'))
                <button type="{{ config('larapages.save_on_enter')?'submit':'button' }}" id="model_save" class="button border is-green is-primary"><i class="fa fa-save"></i><span>{{ trans('larapages::base.save') }}</span></button>
                @endif
                @if ($lp->can('create'))
                <button type="button" id="model_clone" class="button border"><i class="fa fa-clone"></i><span>{{ trans('larapages::base.savecopy') }}</span></button>
                @endif
                @if ($lp->can('delete'))
                <button type="button" id="model_delete" data-confirm="{{ trans('larapages::base.deleteconfirm') }}" class="button border is-red"><i class="fa fa-trash"></i><span>{{ trans('larapages::base.delete') }}</span></button>
                @endif
                <button type="button" id="model_close" class="button border"><i class="fa fa-ban"></i><span>{{ trans('larapages::base.close') }}</span></button>
                <label class="f-right model-id">id:<span id="input_id"></span></label>
            </div>
            <div class="content">
                @foreach($lp->columns() as $id => $column)
                <label for="input_{{ $id }}">
                @if ($column['type'] == 'boolean')
                <input type="hidden" name="{{ $id }}" value="0">
                <input type="checkbox" name="{{ $id }}" id="input_{{ $id }}" value="1">
                @endif
                {{ $lp->locale('title', $column, $id) }}</label>
                @if ($column['type'] == 'string' || $column['type'] == 'password' || $column['type'] == 'date' || $column['type'] == 'number')
                <input class="{{ $column['type'] == 'date' ? 'datepicker' : '' }}" type="{{ $column['type']=='string' || $column['type'] == 'date'?'text':$column['type'] }}" name="{{ $id }}" id="input_{{ $id }}" placeholder="{{ $lp->locale('placeholder', $column, '') }}">
                @if (isset($column['validate']) && in_array('confirmed',explode('|', $column['validate'])))
                    <label for="input_{{ $id }}_confirmation">{{ trans('larapages::base.confirm') }} {{ $lp->locale('title', $column, $id) }}</label>
                    <input type="{{ $column['type']=='string'?'text':$column['type'] }}" name="{{ $id }}_confirmation" id="input_{{ $id }}_confirmation" placeholder="{{ $lp->locale('placeholder', $column, '') }}">
                @endif
                @elseif ($column['type'] == 'select')
                <select name="{{ $id }}" id="input_{{ $id }}">
                    <option value=""></option>
                    @foreach($column['values'] as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                @elseif ($column['type'] == 'text')
                <textarea name="{{ $id }}" id="input_{{ $id }}" rows="5" placeholder="{{ $lp->locale('placeholder', $column, '') }}"></textarea>
                @elseif ($column['type']!='boolean')
                {{$column['type']}}
                @endif
                @endforeach
            </div>
            </form>
        </section>
        @endif
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/i18n/jquery-ui-timepicker-addon-i18n.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/nestedSortable/2.0.0/jquery.mjs.nestedSortable.min.js"></script>
<script>
    modelInit('{{$lp->slug()}}');
</script>
@endsection
