<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Read</div>

                    <div class="card-body">
                        Read a chapter.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {mapGetters} from "vuex";

export default {
    name: "Read",
    beforeCreate() {
        if (this.$route.params.pathMatch) {
            this.$route.params.vol = this.$route.params.pathMatch.split("/")[1];
        }
        delete this.$route.params.pathMatch;
        if (this.$route.params[1]) {
            this.$route.params.ch = this.$route.params[1].split("/")[1];
        }
        delete this.$route.params[1];
        if (this.$route.params[2]) {
            this.$route.params.sub = this.$route.params[2].split("/")[1];
        }
        delete this.$route.params[2];
        if (this.$route.params[3]) {
            this.$route.params.page = this.$route.params[3];
        }
        delete this.$route.params[3];
    },
    mounted() {
        $('#nav-search').hide();
        //this.$store.dispatch('fetchComic');
    },
    methods: {},
    computed: {
        ...mapGetters([
            'comic'
        ])
    }
}
</script>
