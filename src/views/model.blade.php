@extends('larapages::base')

@section('header')
    @if ($lp->can('create'))
    <label for="edit-toggle" class="button"><i class="fa fa-plus-circle"></i>{{ $lp->locale('new', $lp->module(), trans('larapages::base.new')) }}</label>
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
                {!! $lp->listviewData() !!}
                @endif
                @if ($lp->can('create'))
                <label for="edit-toggle" class="button add"><i class="fa fa-plus-circle"></i>{{ $lp->locale('new', $lp->module(), trans('larapages::base.new')) }}</label>
                @endif
            </div>
        </section>
        @if ($lp->can('read'))
        <section id="editview">
            <div class="header">
                @if ($lp->can('update'))
                <button class="button border is-green is-primary"><i class="fa fa-save"></i><span>{{ trans('larapages::base.save') }}</span></button>
                @endif
                @if ($lp->can('create'))
                <button class="button border"><i class="fa fa-clone"></i><span>{{ trans('larapages::base.savecopy') }}</span></button>
                @endif
                @if ($lp->can('delete'))
                <button class="button border is-red"><i class="fa fa-trash"></i><span>{{ trans('larapages::base.delete') }}</span></button>
                @endif
                <button class="button border"><i class="fa fa-ban"></i><span>{{ trans('larapages::base.close') }}</span></button>
            </div>
            <div class="content">
                <form id="model_form">
                @foreach($lp->columns(true) as $id => $column)
                <label for="input_{{ $id }}">
                @if ($column['type'] == 'boolean')
                <input type="checkbox" id="input_{{ $id }}">
                @endif
                {{ $lp->locale('title', $column, $id) }}</label>
                @if ($column['type'] == 'string')
                <input type="text" id="input_{{ $id }}" placeholder="{{ $lp->locale('placeholder', $column, '') }}">
                @elseif ($column['type'] == 'date')
                <input type="date" id="input_{{ $id }}" placeholder="{{ $lp->locale('placeholder', $column, '') }}">
                @elseif ($column['type'] == 'text')
                <textarea id="input_{{ $id }}" placeholder="{{ $lp->locale('placeholder', $column, '') }}"></textarea>
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
    modelInit();
</script>
@endsection
