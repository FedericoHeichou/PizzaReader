let timeout = 0;
let allowed = true;

$(document).ready(function () {
    $("body").keydown(function (e) {
        if (e.which === 37 || e.which === 38 || e.which === 39 || e.which === 40) e.preventDefault();
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
});
