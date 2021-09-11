<template>
    <div v-if="comic != null" id="comic" class="py-sm-4">
        <div class="card">
            <div class="card-header">
                <span class="fas fa-book fa-fw"></span>
                {{ comic.title }}
                <a v-if="comic.id !== null" :href="reader.BASE_URL + 'admin/comics/' + comic.slug" target="_blank">
                    <span class="fas fa-edit fa-fw mr-0" aria-hidden="true" title="Edit"></span>
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-3 col-md-4 text-center">
                        <div class="thumbnail-full float-lg-left">
                            <img :src="comic.thumbnail" class="rounded" :alt="comic.title" :title="comic.title">
                        </div>
                    </div>
                    <div class="col-xl-9 col-md-8">

                        <div v-if="comic.last_chapter != null" class="row border-bottom py-1">
                            <div class="col-md-2 font-weight-bold">
                                <label class="mb-0">{{ reader.__('Last chapter') }}:</label>
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
                                <label class="mb-0">{{ reader.__('Author') }}:</label>
                            </div>
                            <div class="col-md-10">
                                {{ comic.author }}
                            </div>
                        </div>

                        <div class="row border-bottom py-1">
                            <div class="col-md-2 font-weight-bold">
                                <label class="mb-0">{{ reader.__('Artist') }}:</label>
                            </div>
                            <div class="col-md-10">
                                {{ comic.artist }}
                            </div>
                        </div>

                        <div class="row border-bottom py-1">
                            <div class="col-md-2 font-weight-bold">
                                <label class="mb-0">{{ reader.__('Target') }}:</label>
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
                                <label class="mb-0">{{ reader.__('Genres') }}:</label>
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
                                <label class="mb-0">{{ reader.__('Format') }}:</label>
                            </div>
                            <div class="col-md-10">
                                {{ comic.format_id === 1 ? 'Manga' : 'Long Strip (Web Toons)' }}
                            </div>
                        </div>

                        <div class="row border-bottom py-1">
                            <div class="col-md-2 font-weight-bold">
                                <label class="mb-0">{{ reader.__('Adult') }}:</label>
                            </div>
                            <div class="col-md-10">
                                {{ comic.adult ? 'Yes' : 'No' }}
                            </div>
                        </div>

                        <div class="row border-bottom py-1">
                            <div class="col-md-2 font-weight-bold">
                                <label class="mb-0">{{ reader.__('Stats') }}:</label>
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
                                <label class="mb-0">{{ reader.__('Status') }}:</label>
                            </div>
                            <div class="col-md-10">
                                <span class="badge badge-status p-2 text-white" title="Status" :data-status="comic.status ? comic.status.toLowerCase() : ''">{{ comic.status }}</span>
                            </div>
                        </div>

                        <div class="row py-1">
                            <div class="col-md-2 font-weight-bold">
                                <label class="mb-0">{{ reader.__('Description') }}:</label>
                            </div>
                            <div class="col-md-10 text-pre text-justify">{{ comic.description }}</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div id="comic_top_html" class="text-center"></div>

        <div class="card mt-3">
            <div class="card-header">
                <div class="row">
                    <div class="col-sm-9 pt-2">
                        <span class="fas fa-book-open fa-fw"></span>
                        {{ reader.__('Chapters') }}
                    </div>
                    <div class="col-sm-3">
                        <input class="form-control mr-sm-2 ui-autocomplete-input card-search" type="search"
                               :placeholder="reader.__('Filter chapters')" aria-label="Filter" name="filter" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="card-body">

                <div class="row border-bottom py-1 d-sm-flex d-none">
                    <div class="col-sm-auto text-center pr-0">
                        <span class="fa fa-eye fa-fw" title="Read"></span>
                        <span class="fa fa-download fa-fw pl-sm-1" title="Direct download"></span>
                        <span class="fa fa-cloud-download-alt fa-fw" title="External download"></span>
                        <span class="fa fa-file-pdf fa-fw" title="PDF"></span>
                    </div>
                    <div class="col-sm-5 text-left">{{ reader.__('Title') }}</div>
                    <div class="col-sm-auto text-center">
                        <span class="fa fa-globe fa-fw" title="Language" style="width:24px"></span>
                    </div>
                    <div class="col-sm-2 text-success text-left pl-3">
                        <span class="fa fa-users fa-fw" title="Team"></span>
                    </div>
                    <div class="col-sm-2 text-info text-right"><span class="fa fa-eye fa-fw" title="Views"></span></div>
                    <div class="col-sm text-right pl-0"><span class="fa fa-clock fa-fw" title="Publication date"></span></div>
                </div>

                <div v-for="chapter in comic.chapters"
                     :class="'row flex-sm-nowrap text-truncate border-bottom py-1 item' + (chapter.hidden ? ' hidden' : '') + (chapter.licensed ? ' licensed' : '') + (getViewed(chapter.slug_lang_vol_ch_sub) ? ' read text-secondary' : '')">
                    <div class="col-auto text-sm-center pr-0 order-1 overflow-hidden">
                        <span :class="'cursor-pointer fa fa-eye' + (getViewed(chapter.slug_lang_vol_ch_sub) ? '' : '-slash') + ' fa-fw'"
                              :title="'Mark as ' + (getViewed(chapter.slug_lang_vol_ch_sub) ? 'unread' : 'read')" @click="setViewed(chapter.slug_lang_vol_ch_sub)"></span>
                        <a v-if="chapter.chapter_download !== null" :href="reader.API_BASE_URL + chapter.chapter_download">
                            <span class="fa fa-download fa-fw pl-sm-1" title="Direct download"></span>
                        </a>
                        <span v-else class="fa fa-download fa-fw text-secondary pl-sm-1" title="Direct download unavailable"></span>
                        <a v-if="chapter.download_link !== null" :href="chapter.download_link" target="_blank">
                            <span class="fa fa-cloud-download-alt fa-fw" title="External download"></span>
                        </a>
                        <span v-else class="fa fa-cloud-download-alt fa-fw text-secondary" title="External download unavailable"></span>
                        <a v-if="chapter.pdf !== null" :href="reader.API_BASE_URL + chapter.pdf">
                            <span class="fa fa-file-pdf fa-fw" title="PDF"></span>
                        </a>
                        <span v-else class="fa fa-file-pdf fa-fw text-secondary" title="PDF unavailable"></span>
                    </div>
                    <div class="col-sm-5 col-7 text-left order-2 text-truncate">
                        <span class="chapter">
                            <a v-if="chapter.id !== null" :href="reader.BASE_URL + 'admin/comics/' + comic.slug + '/chapters/' + chapter.id" target="_blank">
                                <span class="fas fa-edit fa-fw mr-0" aria-hidden="true" title="Edit"></span>
                            </a>
                            <router-link v-if="!chapter.licensed || chapter.id !== null" :to="chapter.url" class="filter" :title="chapter.full_title">{{ chapter.full_title }}</router-link>
                            <span v-else @click="showPopup" class="filter" :title="chapter.full_title + ' [LICENSED]'">{{ chapter.full_title }}</span>
                        </span>
                    </div>
                    <div class="col-auto text-sm-center pr-sm-3 pr-1 order-sm-3 order-4 overflow-hidden">
                        <span :class="'rounded flag flag-' + chapter.language" :title="chapter.language"></span>
                    </div>
                    <div class="col-sm-2 col-7 text-left pl-sm-3 pl-0 order-sm-4 order-5 text-truncate"
                         :title="chapter.teams.filter(Boolean).map(function(el){return el.name;}).join(', ')">
                        <template v-if="chapter.teams[0] != null">
                            <a :href="chapter.teams[0].url" target="_blank">{{ chapter.teams[0].name }}</a>
                        </template>
                        <template v-if="chapter.teams[1] != null">,&nbsp;
                            <a :href="chapter.teams[1].url" target="_blank">{{ chapter.teams[1].name }}</a>
                        </template>
                    </div>
                    <div class="col-sm-2 col text-info text-right order-sm-6 order-6 overflow-hidden">{{ chapter.views }}</div>
                    <div class="col text-right overflow-hidden order-sm-6 order-3 pl-0" :title="new Date(chapter.published_on)">
                        {{ chapter.time }}
                    </div>
                </div>

            </div>
        </div>

        <div id="comic_bottom_html" class="text-center"></div>

        <div v-if="Object.keys(comic.volume_downloads).length" class="card mt-3">
            <div class="card-header">
                <div class="row">
                    <div class="col-sm-12 pt-2">
                        <span class="fas fa-book-reader fa-fw"></span>
                        {{ reader.__('Volume downloads') }}
                    </div>
                </div>
            </div>
            <div class="card-body">

                <div class="row border-bottom py-1 d-sm-flex d-none">
                    <div class="col-sm-auto text-center pr-0">
                        <span class="fa fa-download fa-fw pl-sm-1" title="Direct download"></span>
                    </div>
                    <div class="col-10 text-left">{{ reader.__('Filename') }}</div>
                </div>

                <div v-for="(download, volume) in comic.volume_downloads" class="row flex-sm-nowrap text-truncate border-bottom py-1 item">
                    <div class="col-auto text-sm-center pr-0 order-1 overflow-hidden">
                        <a :href="reader.API_BASE_URL + download">
                            <span class="fa fa-download fa-fw pl-sm-1" title="Direct download"></span>
                        </a>
                    </div>
                    <div class="col-10 text-left order-2 overflow-hidden">
                        <span class="chapter">
                            <a :href="reader.API_BASE_URL + download">{{ volume }}</a>
                        </span>
                    </div>
                </div>

            </div>
        </div>
        <popup :popupData="popupData" ></popup>
    </div>
    <div v-else ref="notfound"></div>
</template>

<script>
import Vue from 'vue'
import {mapGetters} from "vuex";
import NotFound from './NotFound.vue';
import Popup from'./Popup.vue';

export default {
    name: "Comic",
    components : { NotFound, Popup },
    mounted() {
        $('body').removeClass('body-reader hide-header');
        $('#nav-search').show();
        $('#nav-filter').hide();

        this.$store.dispatch('fetchComic', this.$route)
            .then(() => {
                if (this.$store.getters.comic === null) {
                    const ComponentClass = Vue.extend(NotFound);
                    const instance = new ComponentClass();
                    instance.$mount();
                    if(typeof this.$refs.notfound !== "undefined") this.$refs.notfound.appendChild(instance.$el);
                    const title = 'Error 404' + " | " + this.reader.SITE_NAME;
                    $('title').html(title);
                    $('meta[property="og:title"]').html(title);
                } else {
                    const title = this.$store.getters.comic.title + " | " + this.reader.SITE_NAME;
                    $('title').html(title);
                    $('meta[property="og:title"]').html(title);
                }
                filter_search();
            });
        this.comic != null && this.updateCustomHTML();
        $('html,body').scrollTop(0);
    },
    updated() {
        this.comic != null && this.updateCustomHTML();
    },
    data() {
        return {
            reader: this.$root,
            viewed: this.$root.getCookie('viewed') ? JSON.parse('' + this.$root.getCookie('viewed')) : {},
            popupData : {
                "header" : this.$root.__('Chapter licensed'),
                "body" : this.$root.__('This chapter is licensed and you can\'t read it.'),
                "button" : "Ok",
            }
        }
    },
    methods: {
        getViewed(slug) {
            if(typeof this.viewed[this.$store.getters.comic.slug] === 'undefined') {
                return 0;
            }
            return this.viewed[this.$store.getters.comic.slug][slug] || 0;
        },
        setViewed(slug) {
            if(typeof this.viewed[this.$store.getters.comic.slug] === 'undefined'){
                this.viewed[this.$store.getters.comic.slug] = {};
            }
            this.viewed[this.$store.getters.comic.slug][slug] ^= 1;
            this.reader.setCookie('viewed', JSON.stringify(this.viewed), 3650);
            this.$forceUpdate();
        },
        showPopup() {
            $('#modal-container').modal({show: true, closeOnEscape: true, backdrop: 'static', keyboard: true});
        },
        updateCustomHTML() {
            this.reader.updateCustomHTML('comic_top_html');
            this.reader.updateCustomHTML('comic_bottom_html');
        },
    },
    computed: {
        ...mapGetters([
            'comic'
        ])
    }
}
</script>
