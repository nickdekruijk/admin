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
                @if ($lp->can('read'))
                <div class="folders">
                    {!! LaraPages\Admin\Controllers\MediaController::folders() !!}
                </div>
                @endif
                @if ($lp->can('create'))
                <button class="button add" id="media_newfolder" data-prompt="{{ trans('larapages::base.foldername') }}"><i class="fa fa-plus-circle"></i><span>{{ trans('larapages::base.newfolder') }}</span></button>
                @endif
            </div>
            @endif
        </section>
        @if ($lp->can('read'))
        <section id="editview">
            <div class="header">
                @if ($lp->can('create'))
                <button type="button" id="media_upload" class="button border is-primary is-green"><i class="fa fa-cloud-upload"></i><span>{{ trans('larapages::base.upload') }}</span><span> ({{ trans('larapages::base.max') }} {{ LaraPages\Admin\Controllers\MediaController::uploadLimit() }} MB)</span></button>
                <input data-uploadLimit="{{ LaraPages\Admin\Controllers\MediaController::uploadLimit()*1024*1024 }}" id="fileupload" type="file" name="upl" multiple>
                @endif
                @if ($lp->can('delete'))
                <button type="button" id="media_deletefolder" class="button border is-red" data-confirm="{{ trans('larapages::base.deletefolder') }}"><i class="fa fa-trash"></i><span>{{ trans('larapages::base.deletefolder') }}</span></button>
                @endif
                @if ($lp->can('read'))
                <button type="button" class="button border start active view" data-view="th"><i class="fa fa-th-large"></i></button><button type="button" class="button border mid view" data-view="th-large"><i class="fa fa-th"></i></button><button type="button" class="button border end view" data-view="list"><i class="fa fa-list"></i></button>
                @endif
                <button type="button" id="media_close" class="button border"><i class="fa fa-ban"></i><span>{{ trans('larapages::base.close') }}</span></button>
                <label class="f-right"><span id="current_folder"></span></label>
            </div>
            <div class="content">
                <ul class="upload-progress"></ul>
                <ul class="media"></ul>
            </div>
        </section>
        @endif
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js">
<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.20.0/js/jquery.iframe-transport.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.20.0/js/jquery.fileupload.min.js"></script>
<script>
    mediaInit('{{$lp->slug()}}');
</script>
@endsection
