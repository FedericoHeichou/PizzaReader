<template>
    <div class="container py-sm-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Target</div>

                    <div class="card-body">
                        Target a chapter.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Target",
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
