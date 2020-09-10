$('input[type="checkbox"]').on('change', function(){
    this.value ^= 1;
});

window.addEventListener('load', () => {
    const input_time = $('.convert-timezone');
    if(input_time.html() === "") {
        let date;
        if(input_time.val() && input_time.val() !== '1970-01-01T01:00'){
            date = new Date(input_time.val());
            date.setMinutes(date.getMinutes() - date.getTimezoneOffset()*2);
        } else {
            date = new Date();
            date.setMinutes(date.getMinutes() - date.getTimezoneOffset());
        }
        date.setMilliseconds(null);
        date.setSeconds(null);
        input_time.val(date.toISOString().slice(0, -8));
        $('#timezone').val(Intl.DateTimeFormat().resolvedOptions().timeZone);
    } else {
        const date = new Date(input_time.html());
        date.setMinutes(date.getMinutes() - date.getTimezoneOffset());
        date.setMilliseconds(null);
        date.setSeconds(null);
        input_time.html(date);
    }
});
