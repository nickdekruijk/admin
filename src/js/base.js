function str_slug(text) {
    return text.toString().toLowerCase()
        .replace(/\s+/g, '-')           // Replace spaces with -
        .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
        .replace(/^-+/, '')             // Trim - from start of text
        .replace(/-+$/, '');            // Trim - from end of text
}

function listviewSetColumnWidth() {
    var widths = new Array();
    var maxdepth = 0;
    $('#listview LI SPAN, #listview .header SPAN').css('width', 'auto');
    $('#listview .header SPAN').each(function(e) {
        var w = Math.ceil($(this).width());
        if (!widths[e] || w > widths[e]) widths[e] = w;
    });
    $('#listview LI > DIV').each(function() {
        var l = $(this).position().left;
        if (l > maxdepth) maxdepth = l;
        $(this).children('SPAN').each(function(e) {
            var w = Math.ceil($(this).width());
            if (!widths[e] || w > widths[e]) widths[e] = w;
        });
    });
    $('#listview LI > DIV').each(function() {
        var l = $(this).position().left;
        $(this).children('SPAN').each(function(e) {
            $(this).css('width', widths[e] - (e==0?l-maxdepth:0));
        });
    });
    $('#listview .header SPAN').each(function(e) {
        $(this).css('width', widths[e] + (e==0?maxdepth:0));
    });
}

listviewSetColumnWidth();

$('#listview > .content.sortable.treeview > UL').nestedSortable({
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
$('#listview LI > DIV').click(function() {
    $('#listview LI.active').removeClass('active');
    $(this).parent().addClass('active');
    $('#edit-toggle').prop('checked', true);
});

// Listview treeview hover
$('#listview LI > DIV > I').mouseout(function() {
    $(this).parent().parent().children('UL').removeClass('hover');
});
$('#listview LI > DIV > I').mouseover(function() {
    $(this).parent().parent().children('UL').addClass('hover');
});
// Listview treeview collapse and expand
$('#listview LI > DIV > I').click(function() {
    var child = $(this).parent().parent().children('UL');
    if (child.length) {
        child.toggleClass('closed')
        return false;
    }
});

$(window).ready(function() {
/*     listviewSetColumnWidth(); */
});
/*
$(window).resize(function() {
    listviewSetColumnWidth();
});
*/