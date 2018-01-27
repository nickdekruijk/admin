function mediaShow(slug) {
    loading();
    $.ajax('media/'+slug+'/'+mediaFolder(), {
        cache: false,
    }).done(function(data,status,xhr) {
        $('#current_folder').html(decodeURIComponent($('#listview LI.active').data('id').replace(/\+/gi, ' ')).split('/').join('<span>/</span>'));
        $('#fileupload').fileupload('option','url','media/'+slug+'/'+mediaFolder());
        $('#editview UL.media').html(data);
        loadingDone();
    }).fail(function(xhr,status,error) {
        alert(status);
        loadingDone();
    });
}

function mediaFolder() {
    var id = $('#listview LI.active').data('id');
    return encodeURI(id.replace(/\//gi,'%2F'));
}

function mediaFormatFileSize(bytes) {
    bytes=parseInt(bytes);
    if (bytes >= 1000000000)
        return (bytes / 1000000000).toFixed(2) + ' GB';
    if (bytes >= 1000000)
        return (bytes / 1000000).toFixed(2) + ' MB';
    return (bytes / 1000).toFixed(2) + ' KB';
}

function mediaUpload(slug) {
    $('#fileupload').fileupload({
        dataType: 'json',
        add: function (e, data) {
            var tpl = $('<li><div></div><span class="but"></span>'+data.files[0].name+' (<span class="message">'+mediaFormatFileSize(data.files[0].size)+', <span class="perc">0</span>%</span>)</li>');
            data.context = tpl.appendTo($('.upload-progress'));
            tpl.find('SPAN.but').click(function() {
                if (!tpl.hasClass('done') && !tpl.hasClass('error'))
                    jqXHR.abort();
                tpl.fadeOut(function() {
                    tpl.remove();
                });
            })
            if (parseInt($('#fileupload').attr('data-uploadLimit')) < data.files[0].size)
                tpl.addClass('error').find('span.message').text(mediaFormatFileSize(data.files[0].size)+', file is too large to upload');
/*
            else if (!data.files[0].type)
                tpl.addClass('error').find('span.message').text('Sorry, can\'t upload folders');
*/
            else
                var jqXHR = data.submit();
        },
        progress: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            data.context.find('div').css('width', progress+'%');
            data.context.find('span.perc').text(progress);
            if (progress == 100)
                data.context.addClass('done');
        },
        done: function (e, data) {
            if (data.result.status=='success') {
                $('#listview LI.active').click();
                setTimeout(function() {
                    data.context.fadeOut(1000,function() {
                        data.context.remove();
                    });
                }, 3000)
            } else {
                data.context.addClass('error').removeClass('done').find('span.message').text(data.result.status);
            }
        },
        fail: function (e, data) {
            data.context.addClass('error').removeClass('done').find('span.message').text(data.result.status);
        }
    });
}

function mediaListViewAddClick(slug, element) {
    $(element).click(function() {
        mediaEditViewReset(true, true);
        $(element).addClass('active');
        mediaShow(slug);
        return false;
    });
}

function mediaEditViewReset(checked, dontreset) {
    $('#listview LI.active').removeClass('active');
/*     $('#current_folder').text(''); */
//     $('#editview UL.media').text('');
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
    mediaUpload(slug);
    $('#media_upload').click(function() {
        $('#fileupload').click();
        return false;
    });
    $('#media_close').click(function() {
        mediaEditViewReset(false);
    });
}
