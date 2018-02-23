function modelSortable(slug) {
    $('#listview:not(.treeview) > .content.sortable > UL').sortable({
        items: "> li",
    	handle: 'span',
    	opacity: .6,
    	forcePlaceholderSize: true,
    	placeholder: "ui-state-highlight",
        update: function(event, ui) {
            modelSaveSorting(slug);
        }
    }).disableSelection();
}

function modelNestedSortable(slug) {
    $('#listview.treeview > .content.sortable > UL').nestedSortable({
    	forcePlaceholderSize: true,
    	items: 'li',
    	handle: 'div',
    	isTree: true,
    	listType: 'ul',
    /* 	maxLevels: 3, */
    	opacity: .6,
    	toleranceElement: '> div',
    	startCollapsed: true,
    	placeholder: "ui-state-highlight",
        sort: function(a,b) {
            // When starting sorting store the current parent in data-oldparent so we can use it on relocate to check if parent changed
            var id = $(b.item).data('id');
            var parent = $('LI[data-id='+id+']').parent().parent().data('id');
            $('LI[data-id='+id+']').data('oldparent', parent);
        },
    	relocate: function(a, b) {
            // Done dragging, see what's changed
            var id = $(b.item).data('id');
            var parent = $('LI[data-id='+id+']').parent().parent().data('id');
            var oldparent = $('LI[data-id='+id+']').data('oldparent');
            $('LI[data-id='+id+']').data('oldparent', null)
            // Did parent change? Save new parent do database
            if (parent!=oldparent)
                modelChangeParent(slug, id, parent, oldparent);
            else
                modelSaveSorting(slug, parent);
        	listviewSetColumnWidth();
    	}
    });
}

function sortingDone(msg) {
    if (msg) alert(msg);
    loadingDone();
}

function modelSortIds(parent) {
    var ids = '';
    if (parent > 0) {
        $('LI[data-id='+parent+'] > UL > LI').each(function() {
            if (ids) ids += ',';
            ids += parseInt($(this).data('id'));
        });
    } else {
        $('#listview .content > UL > LI').each(function() {
            if (ids) ids+=',';
            ids+=parseInt($(this).data('id'));
        });
    }
    return ids;
}

function modelChangeParent(slug, id, parent, oldparent) {
    loading();
    $.ajax(slug+'/'+id+'/changeparent', {
        method: 'patch',
        cache: 'false',
        data: 'parent='+parent+'&oldparent='+oldparent+'&ids='+modelSortIds(parent),
    }).done(function(data,status,xhr) {
        sortingDone(data);
    }).fail(function(xhr,status,error) {
        sortingDone(status);
    });
}

function modelSaveSorting(slug, parent) {
    loading();
    $.ajax(slug+'/'+parent+'/sort', {
        method: 'patch',
        cache: 'false',
        data: 'ids='+modelSortIds(parent),
    }).done(function(data,status,xhr) {
        sortingDone(data);
    }).fail(function(xhr,status,error) {
        sortingDone(status);
    });
}

function modelImageBaseName(str) {
    var base = new String(str).substring(str.lastIndexOf('/') + 1);
    if(base.lastIndexOf(".") != -1)
        base = base.substring(0, base.lastIndexOf("."));
    return base;
}

function modelImageCaption(element) {
    var caption = prompt(trans['caption']+' "'+$(element).data('image')+'"', $(element).data('caption'));
    if (caption != null && caption != $(element).data('caption')) {
        $(element).data('caption', caption);
        $(element).find('SPAN').text(caption?caption:modelImageBaseName($(element).data('image')));
        modelUpdateImagesTextarea($(element).parent());
    }
}

function modelUpdateImagesTextarea(element) {
    var text = '';
    $(element).children('LI').each(function() {
        if (text) text += "\n";
        text += $(this).data('image');
        if ($(this).data('caption')) text += '|'+$(this).data('caption');
    });
    $(element).prev('TEXTAREA').val(text);
}

function modelImageDelete(element) {
    var p = $(element).parent().parent();
    $(element).parent().detach();
    modelUpdateImagesTextarea(p);
}

function modelUpdateImages() {
    $('TEXTAREA.images').each(function() {
        var lines = $(this).val().match(/[^\r\n]+/g);
        $(this).next('UL').children('LI').detach();
        $(this).next('UL.sortable').sortable({
            items: "> li",
            update: function() {
                modelUpdateImagesTextarea(this);
            }
        }).disableSelection().removeClass('sortable');
        for (i in lines) {
            var image = lines[i].split('|');
            image.push(image.splice(1).join('|') );
            var src = $(this).data('url') + image[0];
            $(this).next('UL').children('.button').before('<li data-image="'+image[0].replace(/\"/g,'&quot;')+'" data-caption="'+image[1].replace(/\"/g,'&quot;')+'"><img src="'+encodeURI(src).replace(/\+/g,'%2B')+'" alt=""><button class="delete button small is-red"><i class="fa fa-trash"></i></button><span>'+(image[1]?image[1]:modelImageBaseName(image[0]))+'</span></li>');
        }
        $(this).next('UL').children('LI').click(function() {
            modelImageCaption(this);
        });
        $(this).next('UL').children('LI').children('.button.delete').click(function(e) {
            modelImageDelete(this);
            return false;
        });
    });
}

function modelShow(slug, id) {
    loading();
    $.ajax(slug+'/'+id, {
        cache: false,
    }).done(function(data,status,xhr) {
        for (i in data) {
            if ($('#input_'+i).attr('type') == 'checkbox') {
                $('#input_'+i).prop('checked', data[i] == true);
            } else {
                $('#input_'+i).val(data[i]);
            }
            $('#input_'+i+'_confirmation').val(data[i]);
            if ($('#input_'+i).hasClass('tinymce')) {
                tinymce.get('input_'+i).setContent(data[i]?data[i]:'');
            }
        }
        modelUpdateImages();
        loadingDone();
    }).fail(function(xhr,status,error) {
        alert(status);
        loadingDone();
    });
}

function modelValidationError(xhr) {
    var error='';
    // Walk thru the fields that didn't validate and format a nice error.
    for (i in xhr.responseJSON.errors) {
        // Set focus on the first field with an error
        if (!error) $('#input_'+i).focus();
        $('#input_'+i).addClass('error');
        $('LABEL[for=input_'+i+']').append('<span class="errormsg"><i class="fa fa-exclamation-triangle"></i>'+xhr.responseJSON.errors[i]+'</span>')
        error+=xhr.responseJSON.errors[i]+'\n';
    }
    console.log(error);
}

function modelClearErrors() {
    $('#editview LABEL .errormsg').detach();
    $('#editview .error').removeClass('error');
}

function modelInactive(data) {
    if (data.active) {
        $('#listview LI[data-id='+data.id+']').removeClass('inactive');
    } else {
        $('#listview LI[data-id='+data.id+']').addClass('inactive');
    }
}

function modelCreate(slug) {
    loading();
    modelClearErrors();
    $.ajax(slug, {
        cache: false,
        data: $('#model_form').serialize(),
        method: 'post',
    }).done(function(data,status,xhr) {
        $('#listview LI.active').removeClass('active');
        if (listviewTable()) {
            $('#listview .content > UL').append('<li data-id="'+data.id+'" class="active">'+data.li+'</li>');
        } else {
            $('#listview .content > UL').append('<li data-id="'+data.id+'" class="active"><div><i></i>'+data.li+'</div></li>');
        }
        modelInactive(data);
        modelId(data.id);
        modelListViewAddClick(slug, $('#listview LI[data-id='+data.id+']'));
        listviewSetColumnWidth();
        loadingDone();
    }).fail(function(xhr,status,error) {
        if (xhr.status==422) {
            modelValidationError(xhr);
        } else {
            alert(status);
        }
        loadingDone();
    });
}

function modelUpdate(slug, id) {
    loading();
    $('.tinymce').each(function() {
        tinyMCE.get(this.id).save();
    });
    modelClearErrors();
    $.ajax(slug+'/'+id, {
        cache: false,
        data: $('#model_form').serialize(),
        method: 'patch',
    }).done(function(data,status,xhr) {
        if (listviewTable())
            $('#listview LI[data-id='+id+']').html(data.li);
        else {
            $('#listview LI[data-id='+id+'] > DIV').html(data.li);
        }
        modelInactive(data);
        listviewSetColumnWidth();
        loadingDone();
    }).fail(function(xhr,status,error) {
        if (xhr.status==422) {
            modelValidationError(xhr);
        } else {
            alert(status);
        }
        loadingDone();
    });
}

function modelDelete(slug, id) {
    loading();
    $.ajax(slug+'/'+id, {
        cache: false,
        method: 'delete',
    }).done(function(data,status,xhr) {
        if (listviewTable()) {
            $('#listview LI[data-id='+id+']').detach();
        } else {
            $('#listview LI[data-id='+id+']').animate({height:0}, function() {
                $('#listview LI[data-id='+id+']').detach();
                listviewSetColumnWidth();
            });
        }
        modelEditViewReset(false);
        loadingDone();
    }).fail(function(xhr,status,error) {
        alert(status);
        loadingDone();
    });
}

function modelListViewAddClick(slug, element) {
    $(element).click(function() {
        if ($('.ui-sortable-helper').length) return false;
        modelEditViewReset(true, true);
        $(element).addClass('active');
        modelId($(element).data('id'));
        modelShow(slug, $(element).data('id'));
        return false;
    });
}
function modelListViewClick(slug) {
    $('#listview LI').each(function() {
        modelListViewAddClick(slug, this);
    });
    $('BUTTON.model_create').click(function() {
        modelEditViewReset(true);
        modelUpdateImages();
    });
}

function modelEditViewReset(checked, dontreset) {
    modelClearErrors();
    modelId(-1);
    if (!dontreset) $('#model_form')[0].reset();
    $('#listview LI.active').removeClass('active');
    if (checked)
        $('#editview, #listview').addClass('expanded');
    else
        $('#editview, #listview').removeClass('expanded');
}

function modelId(setId) {
    var id = $('#input_id').text();
    if (setId) {
        if (setId==-1) setId = '';
        $('#input_id').text(setId);
        id = setId;
    }
    if (id) {
        $('#model_clone').show();
        $('#model_delete').show();
        $('.model-id').show();
    } else {
        $('#model_clone').hide();
        $('#model_delete').hide();
        $('.model-id').hide();
    }
    return id;
}

function modelEditViewClick(slug) {
    $('#model_form').submit(function(e) {
        e.preventDefault();
    });
    $('#model_save').click(function() {
        $(this).addClass('is-loading');
        if (modelId())
            modelUpdate(slug, modelId());
        else
            modelCreate(slug);
    });
    $('#model_clone').click(function() {
        $(this).addClass('is-loading');
        modelCreate(slug);
    });
    $('#model_close').click(function() {
        modelEditViewReset(false);
    });
    $('#model_delete').click(function() {
        if (confirm($(this).data('confirm'))) {
            $(this).addClass('is-loading');
            modelDelete(slug, modelId());
        }
    });
}

function modelKeydown() {
	$(document).keydown(function(e) {
		var keyCode=e.keyCode || e.which;
		if (keyCode==27) {
    		if ($('#media_browser').length)
    		    $('#media_browser').detach();
    		else
			    $('#model_close').click();
		}
	});
}

var modelAddMediaElement = false;

function modelAddMedia(slug, element) {
    $('BODY').append('<div id="media_browser"><iframe src="media?browse=true"></iframe></div>');
    modelAddMediaElement = element;
    $('#media_browser').click(function() {
        $('#media_browser').detach();
    })
}

function modelAddMediaFile(file) {
    $('#media_browser').detach();
    if(typeof modelAddMediaElement == 'object') {
        modelAddMediaElement.win.document.getElementById(modelAddMediaElement.field_name).value = modelAddMediaElement.media_url+file;
        return true;
    }
    var textarea = $(modelAddMediaElement).parent().prev('textarea');
    var text = textarea.val();
    if (text) text += "\n";
    text += file;
    textarea.val(text);
    modelUpdateImages();
}

function modelInit(slug) {
    $('.datepicker').datepicker({
        showButtonPanel: true,
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 2,
        dateFormat: 'yy-mm-dd',
    });
    $('.datetimepicker').datetimepicker({
        showButtonPanel: true,
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 2,
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss',
    });
    modelKeydown();
    modelNestedSortable(slug);
    modelSortable(slug);
    modelListViewClick(slug);
    modelEditViewClick(slug);
    $('UL.input_images .button.add').click(function() {
        modelAddMedia(slug, this);
    })
}
