function str_slug(text) {
    return text.toString().toLowerCase()
        .replace(/\s+/g, '-')           // Replace spaces with -
        .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
        .replace(/^-+/, '')             // Trim - from start of text
        .replace(/-+$/, '');            // Trim - from end of text
}

// Set the width of the columns/header based on the widest content
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

// Show loading spinner after short delay or hide when done
var isLoading = false;
function loading(done) {
    if (done) {
        clearTimeout(isLoading);
        $('.loading').hide();
    } else {
        isLoading = setTimeout(function() { $('.loading').show() }, 250);
    }
}

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
