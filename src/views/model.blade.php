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
            <div class="content">
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
                @foreach($lp->module('columns') as $id => $column)
                <label for="input_title">{{ $lp->locale('title', $column, $id) }}</label>
                <input type="text" id="input_{{ $id }}" placeholder="{{ $lp->locale('placeholder', $column, '') }}">
                @endforeach
            </div>
        </section>
@endsection
