$('input[type="checkbox"]').on('change', function(){
    this.value ^= 1;
});
