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
            <div class="header">
                @if ($lp->can('update'))
                <button id="model_save" class="button border is-green is-primary"><i class="fa fa-save"></i><span>{{ trans('larapages::base.save') }}</span></button>
                @endif
                @if ($lp->can('create'))
                <button id="model_clone" class="button border"><i class="fa fa-clone"></i><span>{{ trans('larapages::base.savecopy') }}</span></button>
                @endif
                @if ($lp->can('delete'))
                <button id="model_delete" data-confirm="{{ trans('larapages::base.deleteconfirm') }}" class="button border is-red"><i class="fa fa-trash"></i><span>{{ trans('larapages::base.delete') }}</span></button>
                @endif
                <button id="model_close" class="button border"><i class="fa fa-ban"></i><span>{{ trans('larapages::base.close') }}</span></button>
                <label class="f-right">id:<span id="input_id"></span></label>
            </div>
            <div class="content">
                <form id="model_form">
                @foreach($lp->columns(true) as $id => $column)
                <label for="input_{{ $id }}">
                @if ($column['type'] == 'boolean')
                <input type="checkbox" name="{{ $id }}" id="input_{{ $id }}">
                @endif
                {{ $lp->locale('title', $column, $id) }}</label>
                @if ($column['type'] == 'string')
                <input type="text" name="{{ $id }}" id="input_{{ $id }}" placeholder="{{ $lp->locale('placeholder', $column, '') }}">
                @elseif ($column['type'] == 'password')
                <input type="password" name="{{ $id }}" id="input_{{ $id }}" placeholder="{{ $lp->locale('placeholder', $column, '') }}">
                @elseif ($column['type'] == 'date')
                <input type="date" name="{{ $id }}" id="input_{{ $id }}" placeholder="{{ $lp->locale('placeholder', $column, '') }}">
                @elseif ($column['type'] == 'text')
                <textarea name="{{ $id }}" id="input_{{ $id }}" rows="5" placeholder="{{ $lp->locale('placeholder', $column, '') }}"></textarea>
                @elseif ($column['type']!='boolean')
                {{$column['type']}}
                @endif
                @endforeach
                </form>
            </div>
        </section>
        @endif
@endsection

@section('scripts')
<script>
    modelInit('{{$lp->slug()}}');
</script>
@endsection
