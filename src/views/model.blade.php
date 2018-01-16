@extends('larapages::base')

@section('header')
    @if ($lp->can('create'))
    <label for="edit-toggle" class="button"><i class="fa fa-plus-circle"></i>{{ $lp->locale('new', $lp->module(), trans('larapages::base.new')) }}</label>
    @endif
@endsection

@section('view')
    <input type="checkbox" id="edit-toggle">
        <section id="listview">
            @if ($lp->can('read'))
            <div class="header">
                {!! $lp->listviewIndex() !!}
            </div>
            @endif
            <div class="content{{ $lp->module('sortable')?' sortable':'' }}{{ $lp->module('treeview')?' treeview':'' }}">
                @if ($lp->can('read'))
                {!! $lp->listviewData() !!}
                @endif
                @if ($lp->can('create'))
                <label for="edit-toggle" class="button add"><i class="fa fa-plus-circle"></i>{{ $lp->locale('new', $lp->module(), trans('larapages::base.new')) }}</label>
                @endif
            </div>
        </section>
        <section id="editview">
            <div class="header">
                <button class="button border is-green is-primary"><i class="fa fa-save"></i>{{ trans('larapages::base.save') }}</button>
                <button class="button border"><i class="fa fa-clone"></i>{{ trans('larapages::base.savecopy') }}</button>
                <button class="button border is-red"><i class="fa fa-trash"></i>{{ trans('larapages::base.delete') }}</button>
                <label class="button border" for="edit-toggle"><i class="fa fa-ban"></i>{{ trans('larapages::base.close') }}</label>
            </div>
            <div class="content">
                @foreach($lp->columns(true) as $id => $column)
                <label for="input_{{ $id }}">
                @if ($column['type'] == 'boolean')
                <input type="checkbox" id="input_{{ $id }}">
                @endif
                {{ $lp->locale('title', $column, $id) }}</label>
                @if ($column['type'] == 'boolean')
                @elseif ($column['type'] == 'string')
                <input type="text" id="input_{{ $id }}" placeholder="{{ $lp->locale('placeholder', $column, '') }}">
                @elseif ($column['type'] == 'date')
                <input type="date" id="input_{{ $id }}" placeholder="{{ $lp->locale('placeholder', $column, '') }}">
                @elseif ($column['type'] == 'text')
                <textarea id="input_{{ $id }}" placeholder="{{ $lp->locale('placeholder', $column, '') }}"></textarea>
                @else
                {{$column['type']}}
                @endif
                @endforeach
            </div>
        </section>
@endsection
