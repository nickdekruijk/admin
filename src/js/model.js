var modelRoot = null;

function modelSortable(slug) {
    $('#listview:not(.treeview) > .content.sortable > UL').sortable({
        items: "> li",
        handle: 'span',
        opacity: .6,
        forcePlaceholderSize: true,
        placeholder: "ui-state-highlight",
        update: function(event, ui) {
            modelSaveSorting(slug);
        }
    }).disableSelection();
}

function modelNestedSortable(slug) {
    $('#listview.treeview > .content.sortable > UL').nestedSortable({
        forcePlaceholderSize: true,
        items: 'li',
        handle: 'div',
        isTree: true,
        listType: 'ul',
        /* 	maxLevels: 3, */
        opacity: .6,
        toleranceElement: '> div',
        startCollapsed: true,
        placeholder: "ui-state-highlight",
        sort: function(a, b) {
            // When starting sorting store the current parent in data-oldparent so we can use it on relocate to check if parent changed
            var id = $(b.item).data('id');
            var parent = $('LI[data-id=' + id + ']').parent().parent().data('id');
            $('LI[data-id=' + id + ']').data('oldparent', parent);
        },
        relocate: function(a, b) {
            // Done dragging, see what's changed
            var id = $(b.item).data('id');
            var parent = $('LI[data-id=' + id + ']').parent().parent().data('id');
            var oldparent = $('LI[data-id=' + id + ']').data('oldparent');
            $('LI[data-id=' + id + ']').data('oldparent', null)
                // Did parent change? Save new parent do database
            if (parent != oldparent) {
                modelChangeParent(slug, id, parent ? parent : modelRoot, oldparent ? oldparent : modelRoot);
            } else {
                modelSaveSorting(slug, parent);
            }
            listviewSetColumnWidth();
        }
    });
}

function sortingDone(msg) {
    if (msg) alert(msg);
    loadingDone();
}

function modelSortIds(parent) {
    var ids = '';
    if (parent > 0 && parent !== modelRoot) {
        $('LI[data-id=' + parent + '] > UL > LI').each(function() {
            if (ids) ids += ',';
            ids += parseInt($(this).data('id'));
        });
    } else {
        $('#listview .content > UL > LI').each(function() {
            if (ids) ids += ',';
            ids += parseInt($(this).data('id'));
        });
    }
    return ids;
}

function modelChangeParent(slug, id, parent, oldparent) {
    loading();
    $.ajax(slug + '/' + id + '/changeparent', {
        method: 'post',
        cache: 'false',
        data: '_method=patch&parent=' + parent + '&oldparent=' + oldparent + '&ids=' + modelSortIds(parent),
    }).done(function(data, status, xhr) {
        sortingDone(data);
    }).fail(function(xhr, status, error) {
        sortingDone(status);
    });
}

function modelSaveSorting(slug, parent) {
    loading();
    $.ajax(slug + '/' + parent + '/sort', {
        method: 'post',
        cache: 'false',
        data: '_method=patch&ids=' + modelSortIds(parent),
    }).done(function(data, status, xhr) {
        sortingDone(data);
    }).fail(function(xhr, status, error) {
        sortingDone(status);
    });
}

function modelImageBaseName(str) {
    var base = new String(str).substring(str.lastIndexOf('/') + 1);
    if (base.lastIndexOf(".") != -1)
        base = base.substring(0, base.lastIndexOf("."));
    return base;
}

function modelImageCaption(element) {
    var caption = prompt(trans['caption'] + ' "' + $(element).data('image') + '"', $(element).data('caption'));
    if (caption != null && caption != $(element).data('caption')) {
        $(element).data('caption', caption);
        $(element).find('SPAN').text(caption ? caption : modelImageBaseName($(element).data('image')));
        modelUpdateImagesTextarea($(element).parent());
    }
}

function modelUpdateImagesTextarea(element) {
    var text = '';
    $(element).children('LI').each(function() {
        if (text) text += "\n";
        text += $(this).data('image');
        if ($(this).data('caption')) text += '|' + $(this).data('caption');
    });
    $(element).prev('TEXTAREA').val(text);
}

function modelImageDelete(element) {
    var p = $(element).parent().parent();
    $(element).parent().detach();
    modelUpdateImagesTextarea(p);
}

function modelUpdateImages() {
    $('TEXTAREA.images').each(function() {
        var lines = $(this).val().match(/[^\r\n]+/g);
        $(this).next('UL').children('LI').detach();
        $(this).next('UL.sortable').sortable({
            items: "> li",
            update: function() {
                modelUpdateImagesTextarea(this);
            }
        }).disableSelection().removeClass('sortable');
        for (i in lines) {
            var image = lines[i].split('|');
            image.push(image.splice(1).join('|'));
            var src = $(this).data('url') + image[0];
            $(this).next('UL').children('.button').before('<li data-image="' + image[0].replace(/\"/g, '&quot;') + '" data-caption="' + image[1].replace(/\"/g, '&quot;') + '"><img src="' + encodeURI(src).replace(/\+/g, '%2B') + '" alt="' + trans['imagenotfound'] + '"><button class="delete button small is-red"><i class="fa fa-trash"></i></button><span>' + (image[1] ? image[1] : modelImageBaseName(image[0])) + '</span></li>');
        }
        $(this).next('UL').children('LI').click(function() {
            modelImageCaption(this);
        });
        $(this).next('UL').children('LI').children('.button.delete').click(function(e) {
            modelImageDelete(this);
            return false;
        });
    });
}

function updateHTMLview(slug, element) {
    var html = $('#input_' + i).val();
    var element = $('#input_' + i).next('DIV.htmlview');
    element.html(html);
}

function updateArray(slug, element) {
    var array = JSON.parse($('#input_' + i).val());
    var element = $('#input_' + i).next('TABLE.array');
    element.text('');
    for (n in array) {
        if (typeof array[n] === 'object' && array[n] !== null && array[n]['size'] && array[n]['name']) {
            array[n] = '<a target=\"_blank\" href="' + slug + '/' + modelId() + '/download/' + i + '/' + n + '">' + array[n]['name'] + ' (' + Math.ceil(array[n]['size'] / 1024) + ' kB)</a>';
        } else if (typeof array[n] === 'object') {
            var t = new Array();
            for (i in array[n]) {
                t.push(array[n][i]);
            }
            console.log(t);
            array[n] = t.join('</td><td>');
        }
        element.append('<tr><td>' + n.replace('_', '&nbsp;') + '</td><td>' + (array[n] ? array[n] : '') + '</td></tr>');
    }
}

function modelShow(slug, id) {
    loading();
    $.ajax(slug + '/' + id, {
        cache: false,
    }).done(function(data, status, xhr) {
        $('#editview .rows .data').remove();
        for (i in data) {
            if (i.substr(0, 7) == '_pivot.') {
                $('#editview input[type=checkbox].pivot-' + i.substr(7)).prop('checked', false);
                var pivot = data[i].split(',');
                for (n in pivot) {
                    if (pivot[n]) {
                        $('#editview input[type=checkbox][value=' + pivot[n] + '].pivot-' + i.substr(7)).prop('checked', true);
                    }
                }
            } else if ($('#editview input[type=radio][name=' + i + ']').length) {
                $('#editview input[type=radio][name=' + i + ']').each(function() {
                    if ($(this).val() == data[i] || (data[i] == null && !$(this).val())) {
                        $(this).prop('checked', true)
                    }
                });
            } else if ($('#input_' + i).attr('type') == 'checkbox') {
                $('#input_' + i).prop('checked', data[i] == true);
            } else if ($('#input_' + i).is('TABLE') && $('#input_' + i).hasClass('rows')) {
                var rowData = data[i];
                for (n in rowData) {
                    modelAddLine(slug, $('#input_' + i), rowData[n], i)
                }
            } else {
                $('#input_' + i).val(data[i]).change();
            }
            $('#input_' + i + '_confirmation').val(data[i]);
            if ($('#input_' + i).hasClass('tinymce')) {
                tinymce.get('input_' + i).setContent(data[i] ? data[i] : '');
            }
        }
        $('#editview SELECT').each(function() {
            hideColumns(this);
        });
        modelUpdateImages();
        loadingDone();
    }).fail(function(xhr, status, error) {
        alert(status);
        loadingDone();
    });
}

function modelValidationError(xhr) {
    var error = '';
    // Walk thru the fields that didn't validate and format a nice error.
    for (i in xhr.responseJSON.errors) {
        // Set focus on the first field with an error
        if (!error) $('#input_' + i).focus();
        $('#input_' + i).addClass('error');
        $('LABEL[for=input_' + i + ']').append('<span class="errormsg"><i class="fa fa-exclamation-triangle"></i>' + xhr.responseJSON.errors[i] + '</span>')
        error += xhr.responseJSON.errors[i] + '\n';
    }
}

function modelClearErrors() {
    $('#editview LABEL .errormsg').detach();
    $('#editview .error').removeClass('error');
}

function modelInactive(data) {
    if (data.active) {
        $('#listview LI[data-id=' + data.id + ']').removeClass('inactive');
    } else {
        $('#listview LI[data-id=' + data.id + ']').addClass('inactive');
    }
}

var modelFilters = new Object();

function modelFilterListViewApply() {
    $('#listview .table > UL > LI').removeClass('hiddenByFilter');
    $('#listview .table > UL > LI').each(function() {
        for (i in modelFilters) {
            if (modelFilters[i] && $(this).children('SPAN:eq(' + i + ')').text() != modelFilters[i]) {
                $(this).addClass('hiddenByFilter');
            }
        }
    });
}

function modelFilterListView(slug) {
    $('#listview .canfilter').each(function() {
        var column = $(this).data('column');
        var data = new Object();
        $('#listview .table > UL > LI').each(function() {
            var v = $(this).children('SPAN:eq(' + column + ')').text();
            if (!data[v]) {
                data[v] = 1;
            } else {
                data[v]++;
            }
        })
        var dataLI = '';
        for (i in data) {
            if (i) {
                dataLI += '<li' + (modelFilters[column] == i ? ' class="activeFilter"' : '') + '>' + i + '</li>';
            }
        }
        modelFilterListViewApply();
        $(this).children('UL').html(dataLI);
        $(this).children('UL').html($(this).children('UL').children('LI').sort(function(a, b) {
            return $(a).text().toUpperCase().localeCompare($(b).text().toUpperCase());
        }));
        $(this).children('UL').prepend('<li data-clear="true">' + trans['showall'] + '</li>');
        $(this).children('UL').children('LI').click(function() {
            $(this).siblings().removeClass('activeFilter');
            if ($(this).data('clear')) {
                $(this).parent().parent().removeClass('activeFilter');
                modelFilters[column] = false;
            } else {
                $(this).parent().parent().addClass('activeFilter');
                $(this).addClass('activeFilter');
                modelFilters[column] = $(this).text();
            }
            modelFilterListViewApply();
        });
    });
}

function modelCreate(slug, cloneFromId) {
    loading();
    var data = $('#model_form').serialize();
    if (cloneFromId) {
        data += '&__cloneFromId=' + cloneFromId;
    }
    if (modelRoot) {
        data += '&__modelRoot=' + modelRoot;
    }
    modelClearErrors();
    $.ajax(slug, {
        cache: false,
        data: data,
        method: 'post',
    }).done(function(data, status, xhr) {
        $('#listview LI.active').removeClass('active');
        $('#listview .content > UL').replaceWith(data.listview);
        modelFilterListView(slug);
        modelId(data.id);
        $('#listview LI[data-id=' + data.id + ']').addClass('active');
        $('#listview LI').each(function() {
            modelListViewAddClick(slug, this);
        });
        modelNestedSortable(slug);
        modelSortable(slug);
        listviewSetColumnWidth();
        loadingDone();
    }).fail(function(xhr, status, error) {
        if (xhr.status == 422) {
            modelValidationError(xhr);
        } else {
            alert(status + ': ' + xhr.responseJSON.message);
        }
        loadingDone();
    });
}

function modelUpdate(slug, id) {
    loading();
    $('.tinymce').each(function() {
        tinyMCE.get(this.id).save();
    });
    modelClearErrors();
    $.ajax(slug + '/' + id, {
        cache: false,
        data: '_method=patch&' + $('#model_form').serialize(),
        method: 'post',
    }).done(function(data, status, xhr) {
        if (listviewTable())
            $('#listview LI[data-id=' + id + ']').html(data.li);
        else {
            $('#listview LI[data-id=' + id + '] > DIV').html(data.li);
        }
        modelFilterListView(slug);
        modelInactive(data);
        listviewSetColumnWidth();
        loadingDone();
    }).fail(function(xhr, status, error) {
        if (xhr.status == 422) {
            modelValidationError(xhr);
        } else {
            alert(status + ': ' + xhr.responseJSON.message);
        }
        loadingDone();
    });
}

function modelDelete(slug, id) {
    loading();
    $.ajax(slug + '/' + id, {
        cache: false,
        data: '_method=delete',
        method: 'post',
    }).done(function(data, status, xhr) {
        if (listviewTable()) {
            $('#listview LI[data-id=' + id + ']').detach();
        } else {
            $('#listview LI[data-id=' + id + ']').animate({ height: 0 }, function() {
                $('#listview LI[data-id=' + id + ']').detach();
                listviewSetColumnWidth();
            });
        }
        modelEditViewReset(false);
        loadingDone();
    }).fail(function(xhr, status, error) {
        alert(status);
        loadingDone();
    });
}

function modelListViewAddClick(slug, element) {
    $(element).click(function() {
        if ($('.ui-sortable-helper').length) return false;
        modelEditViewReset(true, true);
        $(element).addClass('active');
        modelId($(element).data('id'));
        modelShow(slug, $(element).data('id'));
        return false;
    });
}

function modelListViewClick(slug) {
    $('#listview LI').each(function() {
        modelListViewAddClick(slug, this);
    });
    $('BUTTON.model_create').click(function() {
        modelEditViewReset(true);
        modelUpdateImages();
    });
}

var modelListviewExpandingTimeout;

function modelEditViewReset(checked, dontreset) {
    modelClearErrors();
    modelId(-1);
    if (!dontreset) $('#model_form')[0].reset();
    $('#listview LI.active').removeClass('active');
    if (checked) {
        $('#editview').addClass('expanded');
        modelListviewExpandingTimeout = setTimeout("$('#listview').addClass('expanded')", 600);
    } else {
        if (modelListviewExpandingTimeout) {
            clearTimeout(modelListviewExpandingTimeout);
        }
        $('#editview, #listview').removeClass('expanded');
    }
}

function modelId(setId) {
    var id = $('#input_id').text();
    if (setId) {
        if (setId == -1) setId = '';
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
        modelCreate(slug, modelId());
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
        var keyCode = e.keyCode || e.which;
        if (keyCode == 27) {
            if ($('#media_browser').length)
                $('#media_browser').detach();
            else
                $('#model_close').click();
        }
    });
}

var modelAddMediaElement = false;
var modelAddMediaHelper = {};

function modelAddMedia5(value, filetype, callback) {
    $('BODY').append('<div id="media_browser"><iframe src="media?browse=true"></iframe></div>');
    modelAddMediaHelper['active'] = true;
    modelAddMediaHelper['callback'] = callback;
}

function modelAddMedia(slug, element) {
    $('BODY').append('<div id="media_browser"><iframe src="media?browse=true"></iframe></div>');
    modelAddMediaElement = element;
    $('#media_browser').click(function() {
        $('#media_browser').detach();
    })
}

function modelAddMediaFile(file) {
    $('#media_browser').detach();
    if (typeof modelAddMediaElement == 'object' && modelAddMediaElement.win && modelAddMediaElement.field_name && modelAddMediaElement.media_url) {
        modelAddMediaElement.win.document.getElementById(modelAddMediaElement.field_name).value = modelAddMediaElement.media_url + file;
        return true;
    }
    if (modelAddMediaHelper.active) {
        modelAddMediaHelper['active'] = false;
        modelAddMediaHelper.callback(file);
        return true;
    }
    var textarea = $(modelAddMediaElement).parent().prev('textarea');
    var text = textarea.val();
    if (text) text += "\n";
    text += file;
    textarea.val(text);
    modelUpdateImages();
}

jQuery.expr[':'].Contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
};

function modelSearch(str) {
    $('#listview LI').removeClass('notfound');
    if (str) {
        $('#listview LI:not(:Contains("' + str + '"))').addClass('notfound');
    }
}

function hideColumns(t) {
    $(t).children('option').each(function() {
        if ($(this).data('hide')) {
            $('#editview .content .hiddenBySelect').removeClass('hiddenBySelect');
            return false;
        }
    });
    var hide = $(t).children('option:selected').data('hide');
    if (hide) {
        hide.split(',').forEach(function(hide) {
            $('LABEL[for=input_' + hide + ']').addClass('hiddenBySelect');
            $('#input_' + hide).addClass('hiddenBySelect');
        });
    }
}

function modelAddLine(slug, element, data, column) {
    var tr = $(element).find('TR.template').clone().removeClass('template').addClass('data');
    tr.appendTo($(element)).find('.pivot-delete').click(function() {
        $(this).parent().parent().remove();
    });
    if (data) {
        for (n in data) {
            tr.find('INPUT[data-column=' + column + '_' + n + ']').val(data[n]);
            tr.find('SELECT[data-column=' + column + '_' + n + ']').val(data[n]);
            console.log(n, data[n]);
        }
    }
    // Fix rendering bug that prevents TH from being shown after deleting
    $(element).find('TH').hide().show();
}

function modelInit(slug) {
    $('.datepicker').datepicker({
        showButtonPanel: true,
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 2,
        dateFormat: 'yy-mm-dd',
    });
    $('.datetimepicker').datetimepicker({
        showButtonPanel: true,
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 2,
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss',
    });
    modelKeydown();
    modelNestedSortable(slug);
    modelSortable(slug);
    modelListViewClick(slug);
    modelEditViewClick(slug);
    modelFilterListView(slug);
    $('.button-url').click(function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        href = href.replace('#id#', modelId());
        window.open(href);
    });
    $('UL.input_images .button.add').click(function() {
        modelAddMedia(slug, this);
    });
    $('DIV.rows .button.add').click(function() {
        modelAddLine(slug, $(this).prev());
    });
    $('.header .search INPUT').keyup(function(e) {
        modelSearch(this.value);
    });
    $('.header .search I.fa').click(function() {
        $('.header .search INPUT').focus();
    });
    $('.header .search INPUT').keydown(function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 13) return false;
    });
    $('#editview TEXTAREA.array').on('change', function() {
        updateArray(slug, this);
    });
    $('#editview TEXTAREA.htmlview').on('change', function() {
        updateHTMLview(slug, this);
    });
    $('#editview SELECT').change(function() {
        hideColumns(this);
    });
}
