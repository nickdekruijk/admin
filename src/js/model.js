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
    $.ajax(slug+'/'+id).done(function(data,status,xhr) {
        for (i in data) {
            $('#input_'+i).val(data[i]);
        }
        loadingDone();
    }).fail(function(xhr,status,error) {
        alert(status);
        loadingDone();
    });
}

function modelDelete(slug, id) {
    loading();
    $.ajax(slug+'/'+id, {
        method: 'delete',
    }).done(function(data,status,xhr) {
        $('#listview LI[data-id='+id+']').animate({height:0}, function() { $('#listview LI[data-id='+id+']').detach() });
        modelEditViewReset(false);
        loadingDone();
    }).fail(function(xhr,status,error) {
        alert(status);
        loadingDone();
    });
}

function modelListViewClick(slug) {
    $('#listview LI').click(function() {
        modelEditViewReset(true);
        $(this).addClass('active');
        $('#input_id').text($(this).data('id'));
        modelShow(slug, $(this).data('id'));
    });
    $('BUTTON.model_create').click(function() {
        modelEditViewReset(true);
    });
}

function modelEditViewReset(checked) {
    $('#input_id').text('');
    $('#model_form')[0].reset();
    $('#listview LI.active').removeClass('active');
    $('#edit-toggle').prop('checked', checked);
}

function modelId() {
    return $('#input_id').text();
}

function modelEditViewClick(slug) {
    $('#model_close').click(function() {
        modelEditViewReset(false);
    });
    $('#model_delete').click(function() {
        if (confirm($(this).data('confirm'))) {
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
