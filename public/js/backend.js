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
