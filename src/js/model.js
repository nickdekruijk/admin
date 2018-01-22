function modelNestedSortable() {
    $('#listview.treeview > .content.sortable > UL').nestedSortable({
    	forcePlaceholderSize: true,
    	items: 'li',
    	handle: 'div',
    	placeholder: 'menu-highlight',
    	listType: 'ul',
    /* 	maxLevels: 3, */
    	opacity: .6,
    	toleranceElement: '> div',
    	startCollapsed: true,
    	placeholder: "ui-state-highlight",
    	relocate: function(e) {
        	listviewSetColumnWidth();
    	}
    });
}

function modelShow(slug, id) {
    loading();
    $.ajax(slug+'/'+id, {
        cache: false,
    }).done(function(data,status,xhr) {
        for (i in data) {
            $('#input_'+i).val(data[i]);
            $('#input_'+i+'_confirmation').val(data[i]);
        }
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

function modelCreate(slug) {
    loading();
    modelClearErrors();
    $.ajax(slug, {
        cache: false,
        data: $('#model_form').serialize(),
        method: 'post',
    }).done(function(data,status,xhr) {
        $('#listview LI.active').removeClass('active');
        $('#listview .content > UL').append('<li data-id="'+data.id+'" class="active"><div><i></i>'+data.li+'</div></li>');
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
    modelClearErrors();
    $.ajax(slug+'/'+id, {
        cache: false,
        data: $('#model_form').serialize(),
        method: 'patch',
    }).done(function(data,status,xhr) {
        $('#listview LI[data-id='+id+'] > DIV').html(data.li);
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
        $('#listview LI[data-id='+id+']').animate({height:0}, function() {
            $('#listview LI[data-id='+id+']').detach();
            listviewSetColumnWidth();
        });
        modelEditViewReset(false);
        loadingDone();
    }).fail(function(xhr,status,error) {
        alert(status);
        loadingDone();
    });
}

function modelListViewAddClick(slug, element) {
    $(element).click(function() {
        modelEditViewReset(true);
        $(element).addClass('active');
        modelId($(element).data('id'));
        modelShow(slug, $(element).data('id'));
    });
}
function modelListViewClick(slug) {
    $('#listview LI').each(function() {
        modelListViewAddClick(slug, this);
    });
    $('BUTTON.model_create').click(function() {
        modelEditViewReset(true);
    });
}

function modelEditViewReset(checked) {
    modelClearErrors();
    modelId(-1);
    $('#model_form')[0].reset();
    $('#listview LI.active').removeClass('active');
    $('#edit-toggle').prop('checked', checked);
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
			$('#model_close').click();
		}
	});
}

function modelInit(slug) {
    modelKeydown();
    modelNestedSortable();
    modelListViewClick(slug);
    modelEditViewClick(slug);
}
