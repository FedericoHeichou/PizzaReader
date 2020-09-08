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
    mounted() {
        let header = $('#header');
        header.removeClass('header-fixed');
        header.show();
        $('#nav-search').show();
        $('#nav-filter').hide();
    }
}
</script>
