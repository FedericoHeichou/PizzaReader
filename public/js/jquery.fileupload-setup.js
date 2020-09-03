/* $('#fileupload').fileupload({
    uploadTemplateId: null,
    downloadTemplateId: null,
    uploadTemplate: function (o) {
        var rows = $();
        $.each(o.files, function (index, file) {
            var row = $('<tr class="template-upload fade image show">' +
                '<td><span class="preview"></span></td>' +
                '<td><p class="name"></p><strong class="error text-danger"></strong></td>' +
                '<td>' +
                '   <p class="size"></p>' +
                '   <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">' +
                '       <div class="progress-bar progress-bar-success" style="width:0%;"></div>' +
                '   </div>' +
                '</td>' +
                '<td>' +
                (!index && !o.options.autoUpload ?
                    '<button class="btn btn-primary start">' +
                    '   <i class="fas fa-upload"></i>' +
                    '   <span>Start</span>' +
                    '</button>' : '') +
                (!index ?
                    '<button class="btn btn-warning cancel">' +
                    '   <i class="fas fa-ban"></i>' +
                    '   <span>Cancel</span>\n' +
                    '</button>' : '') +
                '</td>' +
                '</tr>');
            row.find('.name').text(file.name);
            row.find('.size').text(o.formatFileSize(file.size));
            if (file.error) {
                row.find('.error').text(file.error);
            }
            rows = rows.add(row);
        });
        return rows;
    },
    downloadTemplate: function (o) {
        var rows = $();
        $.each(o.files, function (index, file) {
            var row = $('<tr class="template-download fade image show">' +
                '<td><span class="preview"></span></td>' +
                '<td><p class="name"></p>' +
                (file.error ? '<strong class="error text-danger"></strong>' : '') +
                '</td>' +
                '<td><span class="size"></span></td>' +
                '<td>' +
                '   <button class="btn btn-danger delete">' +
                '       <i class="fas fa-trash"></i>' +
                '       <span>Delete</span>' +
                '   </button>' +
                '</td>' +
                '</tr>');
            row.find('.size').text(o.formatFileSize(file.size));
            if (file.error) {
                row.find('.name').text(file.name);
                row.find('.error').text(file.error);
            } else {
                row.find('.name').append($('<a></a>').text(file.name));
                if (file.thumbnailUrl) {
                    row.find('.preview').append(
                        $('<a></a>').append(
                            $('<img>').prop('src', file.thumbnailUrl)
                        )
                    );
                }
                row.find('a')
                    .attr('data-gallery', '')
                    .prop('href', file.url)
                    .prop('target', '_blank');
                row.find('button.delete')
                    .attr('data-type', file.delete_type)
                    .attr('data-url', file.delete_url);
            }
            rows = rows.add(row);
        });
        return rows;
    }
});*/

$(function () {
    let fileupload = $('#fileupload');
    'use strict';
    fileupload.fileupload({
        url: upload_url,
        disableImageResize: /Android(?!.*Chrome)|Opera/.test(
            window.navigator.userAgent
        ),
        maxFileSize: 10485760,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png|webp)$/i
    });

    fileupload.addClass('fileupload-processing');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: fileupload.fileupload('option', 'url'),
        dataType: 'json',
        context: fileupload[0]
    }).always(function () {
        $(this).removeClass('fileupload-processing');
    }).done(function (result) {
        $(this).fileupload('option', 'done').call(this, $.Event('done'), {result: result});
    });

});
