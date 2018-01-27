function mediaShow(slug) {
    loading();
    $.ajax('media/'+slug+'/'+mediaFolder(), {
        cache: false,
    }).done(function(data,status,xhr) {
        $('#current_folder').html(decodeURIComponent($('#listview LI.active').data('id').replace(/\+/gi, ' ')).split('/').join('<span>/</span>'));
        $('#fileupload').fileupload('option','url','media/'+slug+'/'+mediaFolder());
        $('#editview UL.media').html(data);
        $('#editview UL.media LI .button.delete').click(function() {
            mediaDestroy(slug, this);
        });
        $('#editview UL.media LI .button.rename').click(function() {
            mediaRename(slug, this);
        });
        loadingDone();
    }).fail(function(xhr,status,error) {
        alert(status);
        loadingDone();
    });
}

function mediaFolder() {
    var id = $('#listview LI.active').data('id');
    return id ? encodeURI(id.replace(/\//gi,'%2F')) : encodeURI('%2F');
}

function mediaFormatFileSize(bytes) {
    bytes=parseInt(bytes);
    if (bytes >= 1000000000)
        return (bytes / 1000000000).toFixed(2) + ' GB';
    if (bytes >= 1000000)
        return (bytes / 1000000).toFixed(2) + ' MB';
    return (bytes / 1000).toFixed(2) + ' KB';
}

function mediaDestroy(slug, target) {
    var filename = $(target).parent().find('.filename').text();
    if (confirm($(target).data('confirm')+' '+filename+'?')) {
        loading();
        $.ajax('media/'+slug+'/'+mediaFolder(), {
            method: 'delete',
            data: 'filename='+encodeURIComponent(filename),
            cache: 'false',
        }).done(function(data,status,xhr) {
            $(target).parent().fadeOut();
            $('#listview LI.active').html(data);
            listviewSetColumnWidth();
            loadingDone();
        }).fail(function(xhr,status,error) {
            alert(status);
            loadingDone();
        });
    }
}

function mediaRename(slug, target) {
    var filename = $(target).parent().find('.filename').text();
    if (newname = prompt($(target).data('prompt')+' '+filename, filename)) {
        loading();
        $.ajax('media/'+slug+'/'+mediaFolder(), {
            method: 'patch',
            data: 'filename='+encodeURIComponent(filename)+'&newname='+encodeURIComponent(newname),
            cache: 'false',
        }).done(function(data,status,xhr) {
            if (data) {
                alert(data);
                loadingDone();
            } else {
                $('#listview LI.active').click();
            }
        }).fail(function(xhr,status,error) {
            alert(status);
            loadingDone();
        });
    }
}

function mediaNewFolder(slug, target) {
    if (folder = prompt($(target).data('prompt'), $(target).text())) {
        loading();
        $.ajax('media/'+slug+'/'+mediaFolder()+'/folder', {
            method: 'post',
            data: 'folder='+encodeURIComponent(folder),
            cache: 'false',
        }).done(function(data,status,xhr) {
            $('#listview .folders').html(data);
            mediaListViewClicks(slug);
            listviewSetColumnWidth();
            loadingDone();
        }).fail(function(xhr,status,error) {
            alert(xhr.responseJSON.message);
            loadingDone();
        });
    }
}

function mediaDeleteFolder(slug, target) {
    if (confirm($(target).data('confirm')+'?')) {
        loading();
        $.ajax('media/'+slug+'/'+mediaFolder()+'/folder', {
            method: 'delete',
            cache: 'false',
        }).done(function(data,status,xhr) {
            $('#listview LI.active').detach();
            mediaEditViewReset(false);
            listviewSetColumnWidth();
            loadingDone();
        }).fail(function(xhr,status,error) {
            alert(xhr.responseJSON.message);
            loadingDone();
        });
    }
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
                if (data.result.folderRow) {
                    $('#listview LI.active').html(data.result.folderRow);
                    listviewSetColumnWidth();
                }
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

function mediaListViewClicks(slug) {
    $('#listview LI').each(function() {
        mediaListViewAddClick(slug, this);
    });
}

function mediaChangeView(button) {
    $('.button.view.active').removeClass('active');
    $(button).addClass('active');
    $('.button.view').each(function() {
        $('#editview .content').removeClass($(this).data('view'));
    });
    $('#editview .content').addClass($('.button.view.active').data('view'));
}

function mediaInit(slug) {
	$(document).keydown(function(e) {
		var keyCode=e.keyCode || e.which;
		if (keyCode==27) {
			$('#media_close').click();
		}
	});
    mediaListViewClicks(slug);
    mediaUpload(slug);
    $('#media_upload').click(function() {
        $('#fileupload').click();
        return false;
    });
    $('#media_close').click(function() {
        mediaEditViewReset(false);
    });
    $('#media_newfolder').click(function() {
        mediaNewFolder(slug,this);
    });
    $('#media_deletefolder').click(function() {
        mediaDeleteFolder(slug,this);
    });
    $('.button.view').click(function() {
        mediaChangeView(this);
    });
}
