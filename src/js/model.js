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
    $.ajax(slug+'/'+id, {
        method: 'delete',
    }).done(function(data,status,xhr) {
        $('#listview LI[data-id='+id+']').animate({height:0}, function() { $('#listview LI[data-id='+id+']').detach() });
        loadingDone();
    }).fail(function(xhr,status,error) {
        alert(status);
        loadingDone();
    });
}

function modelListViewClick(slug) {
    $('#listview LI').click(function() {
        $('#listview LI.active').removeClass('active');
        $(this).addClass('active');
        $('#edit-toggle').prop('checked', true);
        modelEditViewReset();
        $('#input_id').text($(this).data('id'));
        loading();
        modelShow(slug, $(this).data('id'));
    });
}

function modelEditViewReset() {
    $('#input_id').text('');
    $('#model_form')[0].reset();
}

function modelEditViewClick(slug) {
    $('#model_close').click(function() {
        $('#edit-toggle').prop('checked', false);
        modelEditViewReset();
    });
    $('#model_delete').click(function() {
        if (confirm($(this).data('confirm'))) {
            $('#edit-toggle').prop('checked', false);
            loading();
            modelDelete(slug, $('#input_id').text());
            modelEditViewReset();
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