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

function modelListViewClick(slug) {
    $('#listview LI > DIV').click(function() {
        $('#listview LI.active').removeClass('active');
        $(this).parent().addClass('active');
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
}

function modelInit(slug) {
    modelNestedSortable();
    modelListViewClick(slug);
    modelEditViewClick(slug);
}