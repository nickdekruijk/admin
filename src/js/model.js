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

function modelListViewClick() {
    $('#listview LI > DIV').click(function() {
        $('#listview LI.active').removeClass('active');
        $(this).parent().addClass('active');
        $('#edit-toggle').prop('checked', true);
    });
}

function modelInit() {
    modelNestedSortable();
    modelListViewClick();
}