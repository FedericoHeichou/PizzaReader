<template>
    <div class="container py-sm-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Genre</div>

                    <div class="card-body">
                        Genre a chapter.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Genre",
    mounted() {
        $('body').removeClass('body-reader hide-header');
        $('#nav-search').show();
        $('#nav-filter').hide();
    },
    data() {
        return {
            reader: this.$root,
        }
    },
}
</script>
