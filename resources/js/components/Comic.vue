<template>
    <div  v-if="comic != null" id="comic" class="py-sm-4">
        <div class="card">
            <div class="card-header">
                <span class="fas fa-book fa-fw"></span>
                {{ comic.title }}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-3 col-md-4 text-center">
                        <div class="thumbnail-full float-lg-left">
                            <img :src="comic.thumbnail" class="rounded">
                        </div>
                    </div>
                    <div class="col-xl-9 col-md-8">

                        <div v-if="comic.last_chapter != null" class="row border-bottom py-1">
                            <div class="col-md-2 font-weight-bold">
                                <label class="mb-0">Last chapter:</label>
                            </div>
                            <div class="col-md-10 text-success">
                                <span class="fas fa-book-open fa-fw" aria-hidden="true" title="Last chapter"></span>
                                <router-link :to="comic.last_chapter.url" class="text-success font-weight-bold">
                                    {{ comic.last_chapter.full_title }}
                                </router-link>
                            </div>
                        </div>

                        <div class="row border-bottom py-1">
                            <div class="col-md-2 font-weight-bold">
                                <label class="mb-0">Author:</label>
                            </div>
                            <div class="col-md-10">
                                {{ comic.author }}
                            </div>
                        </div>

                        <div class="row border-bottom py-1">
                            <div class="col-md-2 font-weight-bold">
                                <label class="mb-0">Artist:</label>
                            </div>
                            <div class="col-md-10">
                                {{ comic.artist }}
                            </div>
                        </div>

                        <div class="row border-bottom py-1">
                            <div class="col-md-2 font-weight-bold">
                                <label class="mb-0">Target:</label>
                            </div>
                            <div class="col-md-10">
                                <router-link v-if="comic.target != null" :to="'/targets/' + comic.target.toLowerCase()"
                                             class="badge badge-info p-2 text-white">{{ comic.target }}
                                </router-link>
                                <template v-else>{{ comic.target }}</template>
                            </div>
                        </div>

                        <div class="row border-bottom py-1">
                            <div class="col-md-2 font-weight-bold">
                                <label class="mb-0">Genres:</label>
                            </div>
                            <div class="col-md-10">
                                <template v-for="genre in comic.genres">
                                    <router-link :to="'/genres/' + genre.slug" class="badge badge-info p-2 text-white">
                                        {{ genre.name }}
                                    </router-link>&nbsp;
                                </template>
                            </div>
                        </div>

                        <div class="row border-bottom py-1">
                            <div class="col-md-2 font-weight-bold">
                                <label class="mb-0">Format:</label>
                            </div>
                            <div class="col-md-10">
                                {{ comic.format_id === 1 ? 'Manga' : 'Long Strip (Web Toons)' }}
                            </div>
                        </div>

                        <div class="row border-bottom py-1">
                            <div class="col-md-2 font-weight-bold">
                                <label class="mb-0">Adult:</label>
                            </div>
                            <div class="col-md-10">
                                {{ comic.adult ? 'Yes' : 'No' }}
                            </div>
                        </div>

                        <div class="row border-bottom py-1">
                            <div class="col-md-2 font-weight-bold">
                                <label class="mb-0">Stats:</label>
                            </div>
                            <div class="col-md-10">
                                <ul class="list-inline my-0">
                                    <li class="list-inline-item text-primary">
                                        <span class="fas fa-star fa-fw" aria-hidden="true" title="Rating"></span>
                                        <span v-if="comic.rating != null" title="vote">{{ comic.rating }}</span>
                                        <span v-else title="vote">--.-</span>
                                    </li>
                                    <li class="list-inline-item text-info">
                                        <span class="fas fa-eye fa-fw" aria-hidden="true" title="Views"></span>
                                        <span>{{ comic.views }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="row border-bottom py-1">
                            <div class="col-md-2 font-weight-bold">
                                <label class="mb-0">Status:</label>
                            </div>
                            <div class="col-md-10">
                                {{ comic.status }}
                            </div>
                        </div>

                        <div class="row py-1">
                            <div class="col-md-2 font-weight-bold">
                                <label class="mb-0">Description:</label>
                            </div>
                            <div class="col-md-10 pre-formatted">{{ comic.description }}</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <div class="row">
                    <div class="col-sm-9 pt-2">
                        <span class="fas fa-book-open fa-fw"></span>
                        Chapters
                    </div>
                    <div class="col-sm-3">
                        <input class="form-control mr-sm-2 ui-autocomplete-input card-search" type="search"
                               placeholder="Filter chapters" aria-label="Filter" name="filter" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="card-body">

                <div class="row border-bottom py-1 d-sm-flex d-none">
                    <div class="col-sm-auto text-center pr-0"><span class="fa fa-eye fa-fw" title="Read"></span></div>
                    <div class="col-sm-5 text-left">Title</div>
                    <div class="col-sm-auto text-center">
                        <span class="fa fa-globe fa-fw" title="Language" style="width:24px"></span>
                    </div>
                    <div class="col-sm-2 text-success text-left pl-3">
                        <span class="fa fa-users fa-fw" title="Team"></span>
                    </div>
                    <div class="col-sm-2 text-info text-right"><span class="fa fa-eye fa-fw" title="Views"></span></div>
                    <div class="col-sm text-right pl-sm-0"><span class="fa fa-clock fa-fw" title="Publication date"></span></div>
                </div>

                <div v-for="chapter in comic.chapters"
                     :class="'row flex-sm-nowrap text-truncate border-bottom py-1 item' + (chapter.hidden ? ' hidden' : '')">
                    <div class="col-auto text-sm-center pr-0 order-1 overflow-hidden">
                        <span class="fa fa-eye-slash fa-fw" title="You didn't read it"></span>
                    </div>
                    <div class="col-sm-5 col-9 text-left order-2 overflow-hidden">
                        <span class="chapter">
                            <router-link :to="chapter.url" class="filter">{{ chapter.full_title }}</router-link>
                        </span>
                    </div>
                    <div class="col-auto text-sm-center pr-sm-3 pr-1 order-sm-3 order-4 overflow-hidden">
                        <span :class="'rounded flag flag-' + chapter.language" :title="chapter.language"></span>
                    </div>
                    <div class="col-sm-2 col-7 text-success text-left pl-sm-3 pl-0 order-sm-4 order-5 overflow-hidden">
                        <router-link :to="chapter.teams[0].url">{{ chapter.teams[0].name }}</router-link>
                        <template v-if="chapter.teams[1] != null">,&nbsp;
                            <router-link :to="chapter.teams[1].url">{{ chapter.teams[1].name }}</router-link>
                        </template>
                    </div>
                    <div class="col-sm-2 col text-info text-right order-sm-6 order-6 overflow-hidden">{{ chapter.views }}</div>
                    <div class="col text-right overflow-hidden order-sm-6 order-3 overflow-hidden" :title="new Date(chapter.published_on)">
                        {{ chapter.time }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
import {mapGetters} from 'vuex'

export default {
    name: "Comic",
    mounted() {
        $('body').removeClass('body-reader hide-header');
        $('#nav-search').show();
        $('#nav-filter').hide();

        this.$store.dispatch('fetchComic', this.$route);
    },
    methods: {},
    computed: {
        ...mapGetters([
            'comic'
        ])
    }
}
</script>
