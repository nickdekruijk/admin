@extends('larapages::base')

@section('header')
    @if ($lp->can('create'))
    <button class="button model_create"><i class="fa fa-plus-circle"></i><span>{{ $lp->locale('new', $lp->module(), trans('larapages::base.new')) }}</span></button>
    @endif
@endsection

@section('view')
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
                <button class="button add model_create"><i class="fa fa-plus-circle"></i><span>{{ $lp->locale('new', $lp->module(), trans('larapages::base.new')) }}</span></button>
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
                @if ($column['type'] == 'string' || $column['type'] == 'password' || $column['type'] == 'date' || $column['type'] == 'datetime' || $column['type'] == 'number')
                <input class="{{ $column['type'] == 'date' ? 'datepicker' : '' }}{{ $column['type'] == 'datetime' ? 'datetimepicker' : '' }}" type="{{ $column['type']=='string' || $column['type'] == 'date' || $column['type'] == 'datetime'?'text':$column['type'] }}" name="{{ $id }}" id="input_{{ $id }}" placeholder="{{ $lp->locale('placeholder', $column, '') }}">
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
                @elseif ($column['type'] == 'text' || $column['type'] == 'mediumtext' || $column['type'] == 'longtext')
                <textarea class="{{isset($column['tinymce'])?'tinymce':''}}" name="{{ $id }}" id="input_{{ $id }}" rows="{{$column['type'] == 'mediumtext' ? 10 : ($column['type'] == 'longtext' ? 15 : 5)}}" placeholder="{{ $lp->locale('placeholder', $column, '') }}"></textarea>
                @elseif ($column['type'] == 'image' || $column['type'] == 'images')
                <textarea class="images" name="{{ $id }}" id="input_{{ $id }}" data-url="{{ rtrim(config('larapages.media_url'), '/') }}/"></textarea>
                <ul class="input_images {{ $column['type'] }} {{ $column['type']=='images'?'sortable':'' }}" id="images_{{ $id }}"><button class="button add"><i class="fa fa-plus"></i></button></ul>
                @elseif ($column['type'] != 'boolean')
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.7.4/tinymce.min.js"></script>
<script>
    var trans = {
        'caption': '{{ trans('larapages::base.captionfor') }}',
        'delete': '{{ trans('larapages::base.delete') }}',
    };
    modelInit('{{$lp->slug()}}');
    tinymce.init({
    	    selector:'textarea.tinymce',
    	    theme: 'modern',
    	    menubar: false,
    	    branding: false,
    	    paste_as_text: true,
    	    @if (isset($column['tinymce']['css']))
    	    content_css: "{{ $column['tinymce']['css'] }}",
    	    @endif
            browser_spellcheck: true,
            convert_urls : false,
/*
			file_browser_callback: function(input_id, input_value, type, win) {
				lp_mediaTarget=input_id;
				lp_media=''; //input_value;
				lp_modalFrame(lp_adminpath+'/media/mini');
				return false;
			},
*/
    	    plugins: [
        	    // autoresize advlist autolink link image lists hr anchor searchreplace wordcount visualblocks code table paste contextmenu save textcolor contextmenu emoticons template directionality print preview pagebreak charmap media visualchars fullscreen fullpage visualchars insertdatetime nonbreaking
        	    "autoresize autolink link image lists wordcount visualblocks code table paste contextmenu"
            ],
    	    @if (isset($column['tinymce']['toolbar']))
    	    toolbar: "{{ $column['tinymce']['toolbar'] }}",
    	    @else
    	    // underline hr alignleft aligncenter alignright alignjustify | forecolor backcolor emoticons insertfile underline visualchars searchreplace pagebreak charmap
            toolbar: "code visualblocks | undo redo | styleselect | bold italic | bullist numlist outdent indent | link anchor | image media table",
            @endif
    	    @if (isset($column['tinymce']['formats']))
            style_formats: [
	         	{!! $column['tinymce']['formats'] !!}
	        ],
            @endif
    	});
</script>
@endsection
