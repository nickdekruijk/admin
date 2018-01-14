@extends('larapages::base')

@section('header')
    <label for="edit-toggle" class="button"><i class="fa fa-plus-circle"></i>Create new page</label>
@endsection

@section('view')
    <input type="checkbox" id="edit-toggle">
    @include('larapages::listview')
    @include('larapages::editview')
@endsection
