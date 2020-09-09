<!--suppress XmlDuplicatedId -->
<template>
    <div v-if="chapter != null && comic != null && comic.url != null && chapter.pages.length > 0" id="read"
         class="reader row flex-column no-gutters layout-horizontal fit-horizontal"
         data-renderer="single-page" data-display="fit-both" :data-direction="direction">
        <div class="container reader-controls-container">
            <div class="reader-controls-wrapper bg-reader-controls row no-gutters flex-nowrap" style="z-index:1">
                <div class="d-none d-lg-flex col-auto justify-content-center align-items-center cursor-pointer"
                      id="reader-controls-collapser">
                    <span class="fas fa-caret-right fa-fw arrow-link" aria-hidden="true" title="Collapse menu"></span>
                </div>
                <div class="reader-controls col row no-gutters flex-column flex-nowrap">
                    <div class="reader-controls-title col-auto text-center p-2">
                        <router-link :to="comic.url" class="comic-title" :title="comic.title">
                            {{ comic.title }}
                        </router-link>
                    </div>
                    <div class="reader-controls-chapters col-auto row no-gutters align-items-center">
                        <router-link v-if="chapter.previous != null" :title="chapter.previous.full_title"
                                     class="chapter-link-left col-auto arrow-link" :to="chapter.previous.url">
                            <span class="fas fa-angle-left fa-fw" aria-hidden="true"></span>
                        </router-link>
                        <router-link v-else class="chapter-link-left col-auto arrow-link"
                                         title="Back to comic" :to="comic.url">
                            <span class="fas fa-angle-left fa-fw" aria-hidden="true"></span>
                        </router-link>
                        <div class="col py-2">
                            <select class="form-control col" id="jump-chapter" name="jump-chapter">
                                <template v-for="other_chapter in comic.chapters">
                                    <option v-if="other_chapter.url !== chapter.url" :value="other_chapter.url">
                                        {{ other_chapter.full_chapter ?  other_chapter.full_chapter : other_chapter.full_title }}
                                    </option>
                                    <option v-else value="" selected>
                                        {{ chapter.full_chapter ?  chapter.full_chapter : chapter.full_title }}
                                    </option>
                                </template>
                            </select>
                        </div>
                        <router-link v-if="chapter.next != null" :title="chapter.next.full_title"
                                     class="chapter-link-right col-auto arrow-link" :to="chapter.next.url">
                            <span class="fas fa-angle-right fa-fw" aria-hidden="true"></span>
                        </router-link>
                        <router-link v-else class="chapter-link-right col-auto arrow-link"
                                     title="Back to comic" :to="comic.url">
                            <span class="fas fa-angle-right fa-fw" aria-hidden="true"></span>
                        </router-link>
                        <div class="col-auto py-2 pr-2 d-lg-none">
                            <select class="form-control" id="jump-page" name="jump-page">
                                <template v-for="(chapter_page, index) in chapter.pages">
                                    <option :value="index + 1">{{ index + 1 }}</option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div class="reader-controls-groups no-gutters p-2 text-center font-weight-bold">
                        {{ chapter.title ? chapter.title : chapter.full_title }}
                    </div>
                    <div class="reader-controls-unsupported col-auto row no-gutters p-2 text-danger d-none"></div>
                    <div class="reader-controls-actions col-auto row no-gutters p-1">
                        <div class="col row no-gutters" style="min-width:120px;">
                            <a title="Reader settings" class="btn btn-secondary col m-1" role="button"
                               id="settings-button" data-toggle="modal" data-target="#modal-settings">
                                <span class="fas fa-cog fa-fw"></span><span class="d-none d-lg-inline"> Settings</span>
                            </a>
                            <a title="Hide header" class="btn btn-secondary col m-1" role="button"
                               @click="hideHeader" id="hide-header-button">
                                <span class="far fa-window-maximize fa-fw"></span>
                                <span class="d-none d-lg-inline"> Hide header</span>
                            </a>
                        </div>
                    </div>
                    <div class="reader-controls-mode col-auto d-lg-flex d-none flex-column align-items-start row no-gutters p-2">
                        <div class="col text-center font-weight-bold">Fit display to</div>
                        <div class="col btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-secondary col-4">
                                <input type="radio" data-value="container" data-setting="displayFit" @click="setSetting"
                                       autocomplete="off">Container
                            </label>
                            <label class="btn btn-secondary col-4 active">
                                <input type="radio" data-value="width" data-setting="displayFit" @click="setSetting"
                                       autocomplete="off" checked>Width
                            </label>
                            <label class="btn btn-secondary col-4">
                                <input type="radio" data-value="height" data-setting="displayFit" @click="setSetting"
                                       autocomplete="off">Height
                            </label>
                        </div>
                        <div class="col text-center font-weight-bold mt-2">Page rendering</div>
                        <div class="col btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-secondary col-4">
                                <input type="radio" data-value="double" data-setting="renderingMode" @click="setSetting"
                                       autocomplete="off">Double
                            </label>
                            <template v-if="chapter.format_id === 2">
                                <label class="btn btn-secondary col-4">
                                    <input type="radio" data-value="single" data-setting="renderingMode" @click="setSetting"
                                           autocomplete="off">Single
                                </label>
                                <label class="btn btn-secondary col-4 active">
                                    <input type="radio" data-value="long-strip" data-setting="renderingMode" @click="setSetting"
                                           autocomplete="off" checked>Long Strip
                                </label>
                            </template>
                            <template v-else>
                                <label class="btn btn-secondary col-4 active">
                                    <input type="radio" data-value="single" data-setting="renderingMode" @click="setSetting"
                                           autocomplete="off" checked>Single
                                </label>
                                <label class="btn btn-secondary col-4">
                                    <input type="radio" data-value="long-strip" data-setting="renderingMode" @click="setSetting"
                                           autocomplete="off">Long Strip
                                </label>
                            </template>
                        </div>
                        <div class="col text-center font-weight-bold mt-2">Direction</div>
                        <div class="col btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-secondary col-6 active">
                                <input type="radio" data-value="ltr" data-setting="direction" @click="setSetting"
                                       autocomplete="off" checked>Left to right
                            </label>
                            <label class="btn btn-secondary col-6">
                                <input type="radio" data-value="rtl" data-setting="direction" @click="setSetting"
                                       autocomplete="off">Right to left
                            </label>
                        </div>
                    </div>
                    <div class="reader-controls-footer col-auto mt-auto d-none d-lg-flex justify-content-center">
                        <div class="text-muted text-center text-truncate row flex-wrap justify-content-center p-2 no-gutters">
                            Powered by&nbsp;
                            <a href="https://github.com/FedericoHeichou/PizzaReader" target="_blank">
                                PizzaReader
                            </a>
                        </div>
                    </div>
                    <div class="reader-controls-pages col-auto d-none d-lg-flex row no-gutters align-items-center">
                        <router-link v-if="(valueLeft === -1 && page > 1) ||
                        (valueLeft === 1 && page < chapter.pages.length)" :to="'#' + (page+valueLeft)"
                                     class="page-link-left col-auto arrow-link" id="turn-left"
                                     data-action="page" data-direction="left" data-by="1">
                            <span class="fas fa-angle-left fa-fw" aria-hidden="true" title="Turn page left"></span>
                        </router-link>
                        <router-link v-else-if="chapter.previous != null" :to="chapter.previous.url + '#last'"
                                     class="page-link-left col-auto arrow-link" id="turn-left"
                                     data-action="page" data-direction="left" data-by="1">
                            <span class="fas fa-angle-left fa-fw" aria-hidden="true" title="Turn page left"></span>
                        </router-link>
                        <router-link v-else :to="comic.url"
                                     class="page-link-left col-auto arrow-link" id="turn-left"
                                     data-action="page" data-direction="left" data-by="1">
                            <span class="fas fa-angle-left fa-fw" aria-hidden="true" title="Turn page left"></span>
                        </router-link>

                        <div class="col text-center reader-controls-page-text cursor-pointer">
                            Page <span class="current-page">{{ page }}</span> / <span class="total-pages">{{ chapter.pages.length }}</span>
                        </div>

                        <router-link v-if="(valueRight === 1 && page < chapter.pages.length) ||
                                    (valueRight === -1 && page > 1)" :to="'#' + (page+valueRight)"
                                     class="page-link-right col-auto arrow-link" id="turn-right"
                                     data-action="page" data-direction="right" data-by="1">
                            <span class="fas fa-angle-right fa-fw" aria-hidden="true" title="Turn page right"></span>
                        </router-link>
                        <router-link v-else-if="chapter.next != null" :to="chapter.next.url"
                                     class="page-link-right col-auto arrow-link" id="turn-right"
                                     data-action="page" data-direction="left" data-by="1">
                            <span class="fas fa-angle-right fa-fw" aria-hidden="true" title="Turn page right"></span>
                        </router-link>
                        <router-link v-else :to="comic.url"
                                     class="page-link-right col-auto arrow-link" id="turn-right"
                                     data-action="page" data-direction="left" data-by="1">
                            <span class="fas fa-angle-right fa-fw" aria-hidden="true" title="Turn page right"></span>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>

        <div class="reader-main col row no-gutters flex-column flex-nowrap noselect" style="flex:1">
            <noscript>
                <div class="alert alert-danger text-center">
                    JavaScript is required for this reader to work.
                </div>
            </noscript>
            <div class="reader-images col-auto row no-gutters flex-nowrap m-auto text-center cursor-pointer directional">
                <div class="reader-image-wrapper col-auto my-auto justify-content-center align-items-center noselect nodrag row no-gutters"
                     :data-page="page" :style="'order: ' + page + ';'">
                    <img draggable="false" class="noselect nodrag cursor-pointer"
                         :src="chapter.pages[page-1]" :alt="'Page ' + page">
                </div>
            </div>
            <div class="reader-load-icon">
                <span class="fas fa-circle-notch fa-spin" aria-hidden="true"></span>
            </div>
            <div class="reader-page-bar col-auto d-none d-lg-flex directional">
                <div class="track cursor-pointer row no-gutters">
                    <div class="trail position-absolute h-100 noevents"
                         :style="'width: ' + (page/chapter.pages.length*100) + '%;'">
                        <div class="thumb h-100" :style="'width: ' + (100/page) + '%;'"></div>
                    </div>
                    <div class="notches row no-gutters h-100 w-100 directional">
                        <template v-for="(chapter_page, index) in chapter.pages">
                            <router-link :class="page === index+1 ? 'notch col loaded' : 'notch col'"
                                         :data-page="index+1" @mouseover.native="setNotch(index+1)"
                                         :to="'#' + (index+1)" :style="'order: ' + (index+1) + ';'"></router-link>
                        </template>
                    </div>
                    <div class="notch-display col-auto m-auto px-3 py-1 noevents">
                        {{ hover_page }} / {{ chapter.pages.length }}
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
    mounted() {
        this.setupParams(this.$route.params)
        $('body').addClass('body-reader');
        $('#nav-search').show();
        $('#nav-filter').hide();

        this.$store.dispatch('fetchChapter', this.$route.path)
            .then(() => {
                if(this.$store.getters.chapter != null){
                    this.setPage(this.$route.hash, this.$store.getters.chapter.pages.length);
                }
            });
    },
    beforeRouteUpdate(to, from, next) {
        if(from.path !== to.path){
            this.setupParams(to.params)
            this.$store.dispatch('fetchChapter', to.path)
                .then(() => {
                    if(this.$store.getters.chapter != null){
                        this.setPage(to.hash, this.$store.getters.chapter.pages.length);
                    }
                });
        } else {
            this.setPage(to.hash, this.$store.getters.chapter.pages.length);
        }
        next();
    },
    data() {
        return{
            hover_page: 1,
            displayFit: 'width',
            renderingMode: 'single',
            direction: 'ltr',
            valueLeft: -1,
            valueRight: 1,
        }
    },
    methods: {
        setupParams(params) {
            if (params.pathMatch) {
                params.vol = params.pathMatch.split("/")[1];
            }
            delete params.pathMatch;
            if (params[1]) {
                params.ch = params[1].split("/")[1];
            }
            delete params[1];
            if (params[2]) {
                params.sub = params[2].split("/")[1];
            }
            delete params[2];
        },
        setPage(hash, max_page) {
            let page = 1;
            let requested_page = hash.substring(1);
            if(requested_page === "last" || parseInt(requested_page) > max_page) {
                page = max_page;
            }else {
                page = isNaN(parseInt(requested_page)) || parseInt(requested_page) < 1 ? 1 : parseInt(requested_page);
            }
            this.$store.commit('setPage', page);
            if(history.pushState) {
                history.pushState(null, null, '#' + page);
            } else {
                location.hash = '#' + page;
            }
            this.preloadImagesFrom(page);
        },
        setNotch(page){
            this.hover_page = page;
        },
        hideHeader(){
            let hide_header_button = $('#hide-header-button');
            hide_header_button.toggleClass('active');
            if(hide_header_button.hasClass('active')){
                $('body').addClass('hide-header');
            } else {
                $('body').removeClass('hide-header');
            }
        },
        setSetting(e) {
            const setting = $(e.target).data('setting')
            const value = $(e.target).data('value')
            if(setting === 'direction'){
                this.direction = value;
                this.valueLeft = value === 'ltr' ? -1 : 1
                this.valueRight = value === 'ltr' ? 1 : -1
            }
        },
        preloadImage(url) {
            return new Promise(function(resolve, reject){
                let img = new Image()
                img.onload = function(){
                    resolve(url)
                }
                img.onerror = function(){
                    reject(url)
                }
                img.src = url;
            })
        },
        recursivePreload(page_number, max){
            this.preloadImage(this.$store.getters.chapter.pages[page_number - 1]).then((successUrl) => {
                $('.notch[data-page="' + page_number +'"]').addClass('loaded');
                if(page_number < max) {
                    this.recursivePreload(page_number + 1, max)
                }
            }).catch((errorUrl) => {
                // Nothing
            });
        },
        async preloadImagesFrom(page_number) {
            if(page_number == null) page_number = 1
            this.recursivePreload(page_number, this.$store.getters.chapter.pages.length);
        }
    },
    computed: {
        ...mapGetters([
            'comic',
            'chapter',
            'page',
        ])
    }
}
</script>
