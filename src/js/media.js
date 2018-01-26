function mediaShow(slug, id) {
    loading();
    $.ajax('media/'+slug+'/'+encodeURI(id.replace(/\//gi,'%2F')), {
        cache: false,
    }).done(function(data,status,xhr) {
        $('#current_folder').html(decodeURIComponent(id.replace(/\+/gi, ' ')).split('/').join('<span>/</span>'));
        $('#editview .content').html(data);
        loadingDone();
    }).fail(function(xhr,status,error) {
        alert(status);
        loadingDone();
    });
}

function mediaListViewAddClick(slug, element) {
    $(element).click(function() {
        mediaEditViewReset(true, true);
        $(element).addClass('active');
        mediaShow(slug, $(element).data('id'));
        return false;
    });
}

function mediaEditViewReset(checked, dontreset) {
    $('#listview LI.active').removeClass('active');
/*     $('#current_folder').text(''); */
//     $('#editview .content').text('');
    if (checked)
        $('#editview').addClass('expanded');
    else
        $('#editview').removeClass('expanded');
}

function mediaInit(slug) {
	$(document).keydown(function(e) {
		var keyCode=e.keyCode || e.which;
		if (keyCode==27) {
			$('#media_close').click();
		}
	});
    $('#listview LI').each(function() {
        mediaListViewAddClick(slug, this);
    });
    $('#media_upload').click(function() {
        alert('Upload');
    });
    $('#media_close').click(function() {
        mediaEditViewReset(false);
    });
}
