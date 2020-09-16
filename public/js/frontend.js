function timePassed(date) {
    let diff = new Date().getTime() - new Date(date).getTime();
    if(diff < 0) return "In the future";
    diff = parseInt(diff / 1000);
    if (diff < 60) return diff + " s";
    diff = parseInt(diff / 60);
    if (diff < 60) return diff + " m";
    diff = parseInt(diff / 60);
    if (diff < 24) return diff + " h";
    diff = parseInt(diff / 24);
    if (diff < 30) return diff + " d";
    diff = parseInt(diff / 30);
    if (diff < 12) return diff + " mo";
    diff = parseInt(diff / 12);
    return diff + " y ago";
}

window.timePassed = timePassed;

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

$(document).ready(function () {
    let nav_search = $("#nav-search");
    let results_box = $("#results-box");
    nav_search.on("input", function () {
        results_box.hide();
        results_box.html();
        if ($(this).val().length < 3) return;
        $.ajax({
            type: "GET",
            url: API_BASE_URL + "/search/" + $(this).val(),
            beforeSend: function () {
                nav_search.css("background", "url(\"../public/img/loading.gif\") 176px 7px /24px no-repeat rgb(255, 255, 255)");
            },
            success: function (data) {
                nav_search.css("background", "");
                results_box.show();
                let res = '<ul>';
                if (data['comics'].length === 0) {
                    res += '<li>No results</li>';
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
            }
        });
    });

    nav_search.focusout(function () {
        results_box.fadeOut();
    });

});
