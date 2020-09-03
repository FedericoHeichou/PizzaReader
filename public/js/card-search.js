$(document).ready(function(){
    $(".card-search").on("keyup", function() {
        let value = $(this).val().toLowerCase();
        $(".item h5 > a, .item h5:not(:has(> a))").filter(function() {
            $(this).closest('.item').toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
