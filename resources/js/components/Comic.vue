<template>
    <div  v-if="comic != null" id="comic" class="py-sm-4">
        <div class="card">
            <div class="card-header">
                <span class="fas fa-book fa-fw"></span>
                {{ comic.title }}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="thumbnail-full float-left">
                            <img :src="comic.thumbnail" class="rounded">
                        </div>
                    </div>
                    <div class="col-lg-9">

                        <div v-if="comic.last_chapter != null" class="row border-bottom py-1">
                            <div class="col-lg-2 font-weight-bold">
                                <label class="mb-0">Last chapter:</label>
                            </div>
                            <div class="col-lg-10 text-success">
                                <span class="fas fa-book-open fa-fw" aria-hidden="true" title="Last chapter"></span>
                                <router-link :to="comic.last_chapter.url" class="text-success font-weight-bold">
                                    {{ comic.last_chapter.full_title }}
                                </router-link>
                            </div>
                        </div>

                        <div class="row border-bottom py-1">
                            <div class="col-lg-2 font-weight-bold">
                                <label class="mb-0">Author:</label>
                            </div>
                            <div class="col-lg-10">
                                {{ comic.author }}
                            </div>
                        </div>

                        <div class="row border-bottom py-1">
                            <div class="col-lg-2 font-weight-bold">
                                <label class="mb-0">Artist:</label>
                            </div>
                            <div class="col-lg-10">
                                {{ comic.artist }}
                            </div>
                        </div>

                        <div class="row border-bottom py-1">
                            <div class="col-lg-2 font-weight-bold">
                                <label class="mb-0">Target:</label>
                            </div>
                            <div class="col-lg-10">
                                <router-link v-if="comic.target != null" :to="'/targets/' + comic.target.toLowerCase()"
                                             class="badge badge-info p-2 text-white">{{ comic.target }}
                                </router-link>
                                <template v-else>{{ comic.target }}</template>
                            </div>
                        </div>

                        <div class="row border-bottom py-1">
                            <div class="col-lg-2 font-weight-bold">
                                <label class="mb-0">Genres:</label>
                            </div>
                            <div class="col-lg-10">
                                <template v-for="genre in comic.genres">
                                    <router-link :to="'/genres/' + genre.slug" class="badge badge-info p-2 text-white">
                                        {{ genre.name }}
                                    </router-link>&nbsp;
                                </template>
                            </div>
                        </div>

                        <div class="row border-bottom py-1">
                            <div class="col-lg-2 font-weight-bold">
                                <label class="mb-0">Format:</label>
                            </div>
                            <div class="col-lg-10">
                                {{ comic.format_id === 1 ? 'Manga' : 'Long Strip (Web Toons)' }}
                            </div>
                        </div>

                        <div class="row border-bottom py-1">
                            <div class="col-lg-2 font-weight-bold">
                                <label class="mb-0">Adult:</label>
                            </div>
                            <div class="col-lg-10">
                                {{ comic.adult ? 'Yes' : 'No' }}
                            </div>
                        </div>

                        <div class="row border-bottom py-1">
                            <div class="col-lg-2 font-weight-bold">
                                <label class="mb-0">Stats:</label>
                            </div>
                            <div class="col-lg-10">
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
                            <div class="col-lg-2 font-weight-bold">
                                <label class="mb-0">Status:</label>
                            </div>
                            <div class="col-lg-10">
                                {{ comic.status }}
                            </div>
                        </div>

                        <div class="row py-1">
                            <div class="col-lg-2 font-weight-bold">
                                <label class="mb-0">Description:</label>
                            </div>
                            <div class="col-lg-10 pre-formatted">{{ comic.description }}</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-9 pt-2">
                        <span class="fas fa-book-open fa-fw"></span>
                        Chapters
                    </div>
                    <div class="col-lg-3">
                        <input class="form-control mr-sm-2 ui-autocomplete-input card-search" type="search"
                               placeholder="Filter chapters" aria-label="Filter" name="filter" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="card-body">

                <div class="row border-bottom py-1">
                    <div class="col-auto text-center"><span class="fa fa-eye fa-fw" title="Read"></span></div>
                    <div class="col-lg-5 text-left">Title</div>
                    <div class="col-auto text-center">
                        <span class="fa fa-globe fa-fw" title="Language" style="width:24px"></span>
                    </div>
                    <div class="col-lg-2 text-success text-left">
                        <span class="fa fa-users fa-fw" title="Team"></span>
                    </div>
                    <div class="col-lg-2 text-info text-right"><span class="fa fa-eye fa-fw" title="Views"></span></div>
                    <div class="col text-right"><span class="fa fa-clock fa-fw" title="Publication date"></span></div>
                </div>

                <div v-for="chapter in comic.chapters" class="row flex-nowrap text-truncate border-bottom py-1 item">
                    <div class="col-auto text-center">
                        <span class="fa fa-eye-slash fa-fw" title="You didn't read it"></span>
                    </div>
                    <div class="col-lg-5 text-left">
                        <span class="chapter">
                            <router-link :to="chapter.url" class="filter">{{ chapter.full_title }}</router-link>
                        </span>
                    </div>
                    <div class="col-auto text-center">
                        <span :class="'rounded flag flag-' + chapter.language" :title="chapter.language"></span>
                    </div>
                    <div class="col-lg-2 text-success text-left">
                        <router-link :to="chapter.teams[0].url">{{ chapter.teams[0].name }}</router-link>
                        <template v-if="chapter.teams[1] != null">,&nbsp;
                            <router-link :to="chapter.teams[1].url">{{ chapter.teams[1].name }}</router-link>
                        </template>
                    </div>
                    <div class="col-lg-2 text-info text-right">{{ chapter.views }}</div>
                    <div class="col text-right" :title="new Date(chapter.published_on)">{{ chapter.time }}</div>
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
