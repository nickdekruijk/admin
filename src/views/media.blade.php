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
                <button type="button" id="media_upload" class="button border is-primary is-green"><i class="fa fa-cloud-upload"></i><span>{{ trans('larapages::base.upload') }}</span><span> ({{ trans('larapages::base.max') }} {{ LaraPages\Admin\Controllers\MediaController::uploadLimit() }} MB)</span></button>
                <input data-uploadLimit="{{ LaraPages\Admin\Controllers\MediaController::uploadLimit()*1024*1024 }}" id="fileupload" type="file" name="upl" multiple>
                @endif
                <button type="button" id="media_close" class="button border"><i class="fa fa-ban"></i><span>{{ trans('larapages::base.close') }}</span></button>
                <label class="f-right"><span id="current_folder"></span></label>
            </div>
            <div class="content">
                <ul class="upload-progress">
                    {{--<li class="error"><div></div><span class="but"></span>amphora.interactive.sketch (<span class="message">Sorry, can't upload folders</span>)</li>
                    <li><div style="width: 48%;"></div><span class="but"></span>artisanfiles.pdf (<span class="message">18.13 MB, <span class="perc">48</span>%</span>)</li>
                    <li class="done"><div style="width: 100%;"></div><span class="but"></span>eBook_CRM_Basics.pdf (<span class="message">7.22 MB, <span class="perc">100</span>%</span>)</li>--}}
                </ul>
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
