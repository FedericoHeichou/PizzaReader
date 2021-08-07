function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

const plural = (is_plural) => { return is_plural ? 's' : ''; }

const __ = (message) => { return typeof lang_messages !== 'undefined' && lang_messages[message] || message; }

function timePassed(date, short=false) {
    let diff = new Date().getTime() - new Date(date).getTime();
    if(diff < 0) return 'In the future';
    diff = parseInt(diff / 1000);
    if (diff < 60) return diff + (short ? __(' second' + plural(diff !== 1) + ' ago') : ' s');
    diff = parseInt(diff / 60);
    if (diff < 60) return diff + (short ? __(' minute' + plural(diff !== 1) + ' ago') : ' m');
    diff = parseInt(diff / 60);
    if (diff < 24) return diff + (short ? __(' hour' + plural(diff !== 1) + ' ago') : ' h');
    diff = parseInt(diff / 24);
    if (diff < 30) return diff + (short ? __(' day' + plural(diff !== 1) + ' ago') : ' d');
    diff = parseInt(diff / 30);
    if (diff < 12) return diff + (short ? __(' month' + plural(diff !== 1) + ' ago') : ' mo');
    diff = parseInt(diff / 12);
    return diff + (short ? __(' year' + plural(diff !== 1) + ' ago') : ' y');
}

window.timePassed = timePassed;

$(document).ready(function () {
    let nav_search = $('#nav-search');
    let results_box = $('#results-box');
    nav_search.on('input', function () {
        results_box.hide();
        results_box.html();
        if ($(this).val().length < 3) return;
        $.ajax({
            type: 'GET',
            url: API_BASE_URL + '/search/' + encodeURI($(this).val()),
            beforeSend: function () {
                nav_search.css('background', 'url("../img/loading.gif") 85% 7px /24px no-repeat rgb(255, 255, 255)');
            },
            success: function (data) {
                let res = '<ul>';
                if (data['comics'].length === 0) {
                    res += '<li><div>No results</div></li>';
                } else {
                    data['comics'].forEach(function (comic) {
                        res += '<li class="border-bottom hoverable"><a href="' + escapeHtml(comic.url) +
                            '" class="row no-gutters""><div class="col-auto">' +
                            '<span class="thumbnail-micro" style="background-image: url(' +
                            escapeHtml(comic.thumbnail) + ')"></div><div class="col pl-2">' + escapeHtml(comic.title) +
                            '</div></a></li>';
                    });
                }
                res += '</ul>';
                results_box.html(res);
            },
            error: function () {
                results_box.html('<ul><li><div>No results</div></li></ul>');
            },
            complete: function() {
                nav_search.css('background', '');
                results_box.show();
            }
        });
    });

    nav_search.focusout(function () {
        results_box.fadeOut();
    });

});
