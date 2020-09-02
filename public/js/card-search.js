$(document).ready(function(){
    $(".card-search").on("keyup", function() {
        let value = $(this).val().toLowerCase();
        $(".item h5 a").filter(function() {
            $(this).parent().parent().toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
