@extends('larapages::base')

@section('header')
@endsection

@section('view')
    <input type="checkbox" id="edit-toggle" checked>
        <section id="listview" class="treeview">
            @if ($lp->can('read'))
            <div class="header">
                <span>{{ trans('larapages::base.folders') }}</span>
                <span>{{ trans('larapages::base.files') }}</span>
                <span>{{ trans('larapages::base.size') }}</span>
            </div>
            <div class="content">
                {!! LaraPages\Admin\Controllers\MediaController::folders() !!}
            </div>
            @endif
        </section>
        @if ($lp->can('read'))
        <section id="editview">
            <div class="header">
                @if ($lp->can('create'))
                <button type="button" id="media_upload" class="button border"><i class="fa fa-cloud-upload"></i><span>{{ trans('larapages::base.upload') }}</span></button>
                @endif
                <button type="button" id="media_close" class="button border"><i class="fa fa-ban"></i><span>{{ trans('larapages::base.close') }}</span></button>
                <label class="f-right"><span id="current_folder"></span></label>
            </div>
            <div class="content media">
            </div>
        </section>
        @endif
@endsection

@section('scripts')
<script>
    mediaInit('{{$lp->slug()}}');
</script>
@endsection
