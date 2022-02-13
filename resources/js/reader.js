let timeout = 0;
let allowed = true;
var canChange = true;

$(document).ready(function () {
    $("body").keydown(function (e) {
        if (!allowed) return;
        allowed = false;
        let turn_left = document.getElementById('turn-left');
        let turn_right = document.getElementById('turn-right');
        if (e.which === 37 && turn_left) { // left
            turn_left.click();
        } else if (e.which === 39 && turn_right) { // right
            turn_right.click();
        }
        timeout = setTimeout(function () {
            allowed = true;
        }, 35);
    });

    $(document).bind('scroll',function(e){
        let offset = $('body').hasClass('hide-header') ? 0 : Number(getComputedStyle(document.body, "").fontSize.match(/(\d*(\.\d*)?)px/)[1]) * 3.5
        $('.reader-image-wrapper[rendering=long-strip]').each(function(){
            if ($(this).offset().top < window.pageYOffset + offset + 10 &&
                $(this).offset().top + $(this).height() > window.pageYOffset + offset &&
                window.location.hash !== '#' + $(this).attr('data-page') &&
                canChange){
                window.location.hash = $(this).attr('data-page');
            }
        });
    });
});
