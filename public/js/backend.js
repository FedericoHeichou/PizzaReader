$('input[type="checkbox"]').on('change', function () {
    this.value ^= 1;
});

window.addEventListener('load', () => {
    $('#timezone').val(Intl.DateTimeFormat().resolvedOptions().timeZone);
    const input_time = $('.convert-timezone');
    if (input_time.length && !input_time.val()) {
        let date;
        date = new Date();
        date.setMinutes(date.getMinutes() - date.getTimezoneOffset());
        date.setMilliseconds(null);
        date.setSeconds(null);
        input_time.val(date.toISOString().slice(0, -8));
    }
});

$('.role').on('change', function () {
    let user = $(this).data('user');
    $('#edit-' + user + ' .role').val($(this).val());
    $('#edit-' + user).submit()
});

function randomPassword(length) {
    let psw = '';
    let chars = "abcdefghijklmnopqrstuvwxyz!@#$%^&*()-+<>ABCDEFGHIJKLMNOP1234567890";
    for (let i = 0; i < length; i++) {
        psw += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return psw;
}

$("#toggle-password").on("click", function () {
    $(this).toggleClass("fa-eye fa-eye-slash");
    let new_password = $("#new_password");
    "password" === new_password.attr("type") ? new_password.attr("type", "text") : new_password.attr("type", "password");
});

$("#generate-password").on("click", function () {
    let new_password = $("#new_password");
    new_password.val(randomPassword(16));
    "password" === new_password.attr("type") && $("#toggle-password").click();
    new_password.select();
});
