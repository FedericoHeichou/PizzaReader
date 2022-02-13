$(document).ready(function(){
    filter_search();
});

const filter_search = function() {
    $(".card-search").on("input", function() {
        let value = $(this).val().toLowerCase();
        $(".item .filter").filter(function() {
            $(this).closest('.item').toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
}
