$(function(){
    $("#dark-mode-switch").on("change", function() {
        setCookie("dark", parseInt(getCookie("dark")) ? 0 : 1, 3650);
        const body = $("body");
        body.toggleClass("dark");
        if (body.hasClass("dark")) {
            body.attr("data-bs-theme", "dark");
        } else {
            body.attr("data-bs-theme", "light");
        }
    });
});

function setCookie(cname, cvalue, exdays) {
    let d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    let name = cname + "=";
    let ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
