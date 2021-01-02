<template>
    <div class="row py-sm-4">
        <div v-for="comic in comics" :class="'col-lg-6 pb-1 pt-2 border-bottom item' + (comic.hidden ? ' hidden' : '')">
            <div class="thumbnail float-left mr-2">
                <router-link :to="comic.url"><img :src="comic.thumbnail" :alt="comic.title" :title="comic.title" class="rounded"></router-link>
            </div>
            <div class="text-truncate mb-1 d-flex flex-nowrap align-items-center">
                <h5 class="font-weight-bold mb-0">
                    <a v-if="comic.id !== null" :href="reader.BASE_URL + 'admin/comics/' + comic.slug" target="_blank">
                        <span class="fas fa-edit fa-fw mr-0" aria-hidden="true" title="Edit"></span>
                    </a>
                    <router-link class="ml-1 text-truncate filter" :title="comic.title" :to="comic.url">
                        {{ comic.title }}
                    </router-link>
                </h5>
            </div>
            <ul class="list-inline m-1">
                <li class="list-inline-item text-primary">
                    <span class="fas fa-star fa-fw" aria-hidden="true" title="Rating"></span>
                    <span v-if="comic.rating != null" title="vote">{{ comic.rating }}</span>
                    <span v-else title="vote">--.-</span>
                </li>
                <li class="list-inline-item text-info">
                    <span class="fas fa-eye fa-fw" aria-hidden="true" title="Views"></span>
                    <span>{{ comic.views }}</span>
                </li>
                <li v-if="comic.last_chapter != null" class="text-success">
                    <span class="fas fa-book-open fa-fw" aria-hidden="true" title="Last chapter"></span>
                    <router-link :to="comic.last_chapter.url" class="text-success font-weight-bold">{{ comic.last_chapter.full_title }}</router-link>
                </li>
            </ul>
            <div class="description pre-formatted">{{ comic.description }}</div>
        </div>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';

export default {
    name: "Comics",
    mounted() {
        $('body').removeClass('body-reader hide-header');
        $('#nav-search').hide();
        $('#nav-filter').show();
        if (this.$route.params.target !== undefined || this.$route.params.genre !== undefined)
            this.$store.dispatch('fetchComicsFiltered', this.$route.path);
        else
            this.$store.dispatch('fetchComics');
    },
    beforeRouteUpdate(to, from, next) {
        if (this.$route.params.target !== undefined || this.$route.params.genre !== undefined)
            this.$store.dispatch('fetchComicsFiltered', this.$route.path);
        else
            this.$store.dispatch('fetchComics');
        next();
    },
    data() {
        return {
            reader: this.$root,
        }
    },
    methods: {},
    computed: {
        ...mapGetters([
            'comics',
        ])
    }
}
</script>
