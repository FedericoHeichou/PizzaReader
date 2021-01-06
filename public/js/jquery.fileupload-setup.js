$(function () {
    let fileupload = $('#fileupload');
    'use strict';
    fileupload.fileupload({
        url: fileupload.attr('action'),
        disableImageResize: true,
        maxFileSize: 104857600,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png|webp)$/i
    });

    fileupload.fileupload('option', 'done').call(fileupload, $.Event('done'), {result: old_files});

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
