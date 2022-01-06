<!--suppress XmlDuplicatedId -->
<template>
    <div v-if="chapter != null && max_page > 0" id="reader"
         :class="'row flex-column no-gutters layout-horizontal' + (hide_sidebar ? ' hide-sidebar' : '')"
         :data-renderer="renderingMode" :data-display="displayFit" :data-direction="direction">
        <div id="reader-controls-container" class="container">
            <div id="reader-controls-wrapper" class="bg-reader-controls row no-gutters flex-nowrap">
                <div class="d-none d-lg-flex col-auto justify-content-center align-items-center cursor-pointer"
                     id="reader-controls-collapser" data-setting="hide-sidebar" :data-value="hide_sidebar^1"
                     @click="setSettings">
                    <span class="fas fa-caret-right fa-fw arrow-link" aria-hidden="true" title="Collapse menu"
                          data-setting="hide-sidebar" :data-value="hide_sidebar^1"></span>
                </div>
                <div id="reader-controls" class="col row no-gutters flex-column flex-nowrap">
                    <div id="reader-controls-title" class="col-auto text-center p-2">
                        <router-link :to="comic.url" class="comic-title" :title="comic.title">
                            {{ comic.title }}
                        </router-link>
                    </div>
                    <div id="reader-controls-chapters" class="col-auto row no-gutters align-items-center">
                        <router-link v-if="chapter.previous != null" :title="chapter.previous.full_title"
                                     id="chapter-link-left" class="col-auto arrow-link" :to="chapter.previous.url">
                            <span class="fas fa-angle-left fa-fw" aria-hidden="true"></span>
                        </router-link>
                        <router-link v-else id="chapter-link-left" class="col-auto arrow-link"
                                     title="Back to comic" :to="comic.url">
                            <span class="fas fa-angle-left fa-fw" aria-hidden="true"></span>
                        </router-link>
                        <div class="col py-2">
                            <select class="form-control col" id="jump-chapter" name="jump-chapter" @change="jumpChapter">
                                <template v-for="other_ch in comic.chapters">
                                    <option v-if="other_ch.url !== chapter.url" :value="other_ch.url">
                                        {{ other_ch.full_chapter ? other_ch.full_chapter : other_ch.full_title }}
                                    </option>
                                    <option v-else value="" selected>
                                        {{ chapter.full_chapter ? chapter.full_chapter : chapter.full_title }}
                                    </option>
                                </template>
                            </select>
                        </div>
                        <router-link v-if="chapter.next != null" :title="chapter.next.full_title"
                                     id="chapter-link-right" class="col-auto arrow-link" :to="chapter.next.url">
                            <span class="fas fa-angle-right fa-fw" aria-hidden="true"></span>
                        </router-link>
                        <router-link v-else id="chapter-link-right" class="col-auto arrow-link"
                                     title="Back to comic" :to="comic.url">
                            <span class="fas fa-angle-right fa-fw" aria-hidden="true"></span>
                        </router-link>
                        <div class="col-auto py-2 pr-2 d-lg-none">
                            <select id="jump-page" class="form-control" name="jump-page" @change="jumpPage">
                                <template v-for="(ch_page, index) in chapter.pages">
                                    <option v-if="page !== index+1" :value="index + 1">
                                        {{ index + 1 }}
                                    </option>
                                    <option v-else :value="index + 1" selected>
                                        {{ index + 1 }}
                                    </option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div id="reader-controls-groups" class="no-gutters p-2 text-center font-weight-bold">
                        <p class="mb-1">{{ chapter.title ? chapter.title : chapter.full_title }}</p>
                        <a v-if="chapter.chapter_download !== null" :href="reader.API_BASE_URL + chapter.chapter_download">
                            <span class="fa fa-download fa-fw" title="Direct download"></span>
                        </a>
                        <span v-else class="fa fa-download fa-fw text-secondary" title="Direct download unavailable"></span>
                        <a v-if="chapter.download_link !== null" :href="chapter.download_link" target="_blank">
                            <span class="fa fa-cloud-download-alt fa-fw" title="External download"></span>
                        </a>
                        <span v-else class="fa fa-cloud-download-alt fa-fw text-secondary" title="External download unavailable"></span>
                        <a v-if="chapter.pdf !== null" :href="reader.API_BASE_URL + chapter.pdf">
                            <span class="fa fa-file-pdf fa-fw" title="PDF"></span>
                        </a>
                        <span v-else class="fa fa-file-pdf fa-fw text-secondary" title="PDF unavailable"></span>
                        <a v-if="chapter.id !== null" :href="reader.BASE_URL + 'admin/comics/' + comic.slug + '/chapters/' + chapter.id" target="_blank">
                            <span class="fas fa-edit fa-fw mr-0" aria-hidden="true" title="Edit"></span>
                        </a>
                        <ul class="ratings">
                            <li :class="'star' + (vote(chapter.vote_id) === 5 ? ' selected' : '')" data-vote="5" @click="sendVote"></li>
                            <li :class="'star' + (vote(chapter.vote_id) === 4 ? ' selected' : '')" data-vote="4" @click="sendVote"></li>
                            <li :class="'star' + (vote(chapter.vote_id) === 3 ? ' selected' : '')" data-vote="3" @click="sendVote"></li>
                            <li :class="'star' + (vote(chapter.vote_id) === 2 ? ' selected' : '')" data-vote="2" @click="sendVote"></li>
                            <li :class="'star' + (vote(chapter.vote_id) === 1 ? ' selected' : '')" data-vote="1" @click="sendVote"></li>
                        </ul>
                        {{ reader.__('Current rating') }}: {{ chapter.rating || 'N/A' }}
                    </div>
                    <div id="reader-controls-actions" class="col-auto row no-gutters p-1">
                        <div class="col row no-gutters">
                            <a title="Reader settings" class="btn btn-secondary col m-1 d-inline d-lg-none" role="button"
                               id="settings-button" @click="toggleSettings">
                                <span class="fas fa-cog fa-fw"></span><span class="d-none d-lg-inline"> Settings</span>
                            </a>
                            <a title="Hide header" class="btn btn-secondary col m-1" role="button" @click="setSettings"
                               id="hide-header-button" :data-value="hide_header^1" data-setting="hide-header">
                                <span class="far fa-window-maximize fa-fw" :data-value="hide_header^1"
                                      data-setting="hide-header"></span>
                                <span class="d-none d-lg-inline" :data-value="hide_header^1"
                                      data-setting="hide-header"> {{ reader.__('Hide header') }}</span>
                            </a>
                        </div>
                    </div>
                    <div id="reader-controls-mode"
                         class="col-auto d-lg-flex d-none flex-column align-items-start row no-gutters p-2">
                        <div class="col text-center font-weight-bold">{{ reader.__('Fit display to') }}</div>
                        <div class="col btn-group btn-group-toggle" data-toggle="buttons">
                            <label v-if="displayFit === 'fit-both'" class="btn btn-secondary col-3 active">
                                <input type="radio" data-value="fit-both" data-setting="displayFit" @click="setSettings"
                                       autocomplete="off" onkeydown="event.preventDefault()" checked>{{ reader.__('Both') }}
                            </label>
                            <label v-else class="btn btn-secondary col-3">
                                <input type="radio" data-value="fit-both" data-setting="displayFit" @click="setSettings"
                                       autocomplete="off" onkeydown="event.preventDefault()">{{ reader.__('Both') }}
                            </label>
                            <label v-if="displayFit === 'fit-width'" class="btn btn-secondary col-3 active">
                                <input type="radio" data-value="fit-width" data-setting="displayFit" @click="setSettings"
                                       autocomplete="off" onkeydown="event.preventDefault()" checked>{{ reader.__('Width') }}
                            </label>
                            <label v-else class="btn btn-secondary col-3">
                                <input type="radio" data-value="fit-width" data-setting="displayFit" @click="setSettings"
                                       autocomplete="off" onkeydown="event.preventDefault()">{{ reader.__('Width') }}
                            </label>
                            <label v-if="displayFit === 'fit-height'" class="btn btn-secondary col-3 active">
                                <input type="radio" data-value="fit-height" data-setting="displayFit"
                                       @click="setSettings"
                                       autocomplete="off" onkeydown="event.preventDefault()" checked>{{ reader.__('Height') }}
                            </label>
                            <label v-else class="btn btn-secondary col-3">
                                <input type="radio" data-value="fit-height" data-setting="displayFit"
                                       @click="setSettings"
                                       autocomplete="off" onkeydown="event.preventDefault()">{{ reader.__('Height') }}
                            </label>
                            <label v-if="displayFit === 'no-resize'" class="btn btn-secondary col-3 active">
                                <input type="radio" data-value="no-resize" data-setting="displayFit" @click="setSettings"
                                       autocomplete="off" onkeydown="event.preventDefault()" checked>{{ reader.__('Full') }}
                            </label>
                            <label v-else class="btn btn-secondary col-3">
                                <input type="radio" data-value="no-resize" data-setting="displayFit" @click="setSettings"
                                       autocomplete="off" onkeydown="event.preventDefault()">{{ reader.__('Full') }}
                            </label>
                        </div>
                        <div class="col text-center font-weight-bold mt-2">{{ reader.__('Page rendering') }}</div>
                        <div class="col btn-group btn-group-toggle" data-toggle="buttons">
                            <label v-if="renderingMode === 'double-page'" class="btn btn-secondary col-4 active">
                                <input type="radio" data-value="double-page" data-setting="renderingMode"
                                       @click="setSettings"
                                       autocomplete="off" onkeydown="event.preventDefault()" checked>{{ reader.__('Double') }}
                            </label>
                            <label v-else class="btn btn-secondary col-4">
                                <input type="radio" data-value="double-page" data-setting="renderingMode"
                                       @click="setSettings"
                                       autocomplete="off" onkeydown="event.preventDefault()">{{ reader.__('Double') }}
                            </label>
                            <label v-if="renderingMode === 'single-page'" class="btn btn-secondary col-4 active">
                                <input type="radio" data-value="single-page" data-setting="renderingMode"
                                       @click="setSettings"
                                       autocomplete="off" onkeydown="event.preventDefault()" checked>{{ reader.__('Single') }}
                            </label>
                            <label v-else class="btn btn-secondary col-4">
                                <input type="radio" data-value="single-page" data-setting="renderingMode"
                                       @click="setSettings"
                                       autocomplete="off" onkeydown="event.preventDefault()">{{ reader.__('Single') }}
                            </label>
                            <label v-if="renderingMode === 'long-strip'" class="btn btn-secondary col-4 active">
                                <input type="radio" data-value="long-strip" data-setting="renderingMode"
                                       @click="setSettings"
                                       autocomplete="off" onkeydown="event.preventDefault()" checked>{{ reader.__('Long Strip') }}
                            </label>
                            <label v-else class="btn btn-secondary col-4">
                                <input type="radio" data-value="long-strip" data-setting="renderingMode"
                                       @click="setSettings"
                                       autocomplete="off" onkeydown="event.preventDefault()">{{ reader.__('Long Strip') }}
                            </label>
                        </div>
                        <div class="col text-center font-weight-bold mt-2">{{ reader.__('Direction') }}</div>
                        <div class="col btn-group btn-group-toggle" data-toggle="buttons">
                            <label v-if="direction === 'ltr'" class="btn btn-secondary col-6 active">
                                <input type="radio" data-value="ltr" data-setting="direction" @click="setSettings"
                                       autocomplete="off" onkeydown="event.preventDefault()" checked>{{ reader.__('Left to right') }}
                            </label>
                            <label v-else class="btn btn-secondary col-6">
                                <input type="radio" data-value="ltr" data-setting="direction" @click="setSettings"
                                       autocomplete="off" onkeydown="event.preventDefault()">{{ reader.__('Left to right') }}
                            </label>
                            <label v-if="direction === 'rtl'" class="btn btn-secondary col-6 active">
                                <input type="radio" data-value="rtl" data-setting="direction" @click="setSettings"
                                       autocomplete="off" onkeydown="event.preventDefault()" checked>{{ reader.__('Right to left') }}
                            </label>
                            <label v-else class="btn btn-secondary col-6">
                                <input type="radio" data-value="rtl" data-setting="direction" @click="setSettings"
                                       autocomplete="off" onkeydown="event.preventDefault()">{{ reader.__('Right to left') }}
                            </label>
                        </div>
                    </div>

                    <div id="reader_html" class="text-center d-none d-lg-block"></div>

                    <div id="reader-controls-footer" class="col-auto mt-auto d-none d-lg-flex justify-content-center">
                        <div
                            class="text-muted text-center text-truncate row flex-wrap justify-content-center p-2 no-gutters">
                            Powered by&nbsp;
                            <a href="https://github.com/FedericoHeichou/PizzaReader" target="_blank">
                                PizzaReader
                            </a>
                        </div>
                    </div>
                    <div id="reader-controls-pages" class="col-auto d-none d-lg-flex row no-gutters align-items-center">
                        <router-link v-if="(valueLeft === -1 && page > 1) ||
                        (valueLeft === 1 && page < max_page)" :to="'#' + (page+valueLeft)"
                                     id="page-link-left" class="col-auto arrow-link">
                            <span class="fas fa-angle-left fa-fw" aria-hidden="true" title="Turn page left"
                                  @click="needToRefresh = true" id="turn-left"></span>
                        </router-link>
                        <router-link v-else-if="chapter.previous != null" :to="chapter.previous.url + '#last'"
                                     id="page-link-left" class="col-auto arrow-link">
                            <span class="fas fa-angle-left fa-fw" aria-hidden="true" title="Turn page left"
                                  @click="needToRefresh = true" id="turn-left"></span>
                        </router-link>
                        <router-link v-else :to="comic.url" id="page-link-left" class="col-auto arrow-link">
                            <span class="fas fa-angle-left fa-fw" aria-hidden="true" title="Turn page left"
                                  @click="needToRefresh = true" id="turn-left"></span>
                        </router-link>

                        <div id="reader-controls-page-text" class="col text-center cursor-pointer">
                            {{ reader.__('Page') }} <span class="current-page">{{ page }}</span> /
                            <span class="total-pages"> {{ max_page }} </span>
                        </div>

                        <router-link v-if="(valueRight === 1 && page < max_page) ||
                                    (valueRight === -1 && page > 1)" :to="'#' + (page+valueRight)"
                                     id="page-link-right" class="col-auto arrow-link">
                            <span class="fas fa-angle-right fa-fw" aria-hidden="true" title="Turn page right"
                                  @click="needToRefresh = true" id="turn-right"></span>
                        </router-link>
                        <router-link v-else-if="chapter.next != null" :to="chapter.next.url"
                                     id="page-link-right" class="col-auto arrow-link">
                            <span class="fas fa-angle-right fa-fw" aria-hidden="true" title="Turn page right"
                                  @click="needToRefresh = true" id="turn-right"></span>
                        </router-link>
                        <router-link v-else :to="comic.url" id="page-link-right" class="col-auto arrow-link">
                            <span class="fas fa-angle-right fa-fw" aria-hidden="true" title="Turn page right"
                                  @click="needToRefresh = true" id="turn-right"></span>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>

        <div id="reader-main" class="col row no-gutters flex-column flex-nowrap noselect">
            <noscript>
                <div class="alert alert-danger text-center">
                    JavaScript is required for this reader to work.
                </div>
            </noscript>
            <div id="banner_top" class="text-center"></div>
            <div @click="clickPage" id="reader-images" :rendering="renderingMode"
                class="col-auto row no-gutters flex-nowrap m-auto text-center cursor-pointer directional">
                <template v-if="renderingMode === 'single-page'">
                    <div :data-page="page" :style="'order: ' + page + ';'" :rendering="renderingMode"
                         class="reader-image-wrapper col-auto my-auto justify-content-center align-items-center noselect nodrag row no-gutters">
                    </div>
                </template>
                <template v-else-if="renderingMode === 'double-page'">
                    <div :data-page="page" :style="'order: ' + page + ';'" :rendering="renderingMode"
                         class="reader-image-wrapper col-auto my-auto justify-content-center align-items-center noselect nodrag row no-gutters">
                    </div>
                    <div v-if="chapter.pages[page] != null" :data-page="page+1" :style="'order: ' + (page+1) + ';'" :rendering="renderingMode"
                         class="reader-image-wrapper col-auto my-auto justify-content-center align-items-center noselect nodrag row no-gutters">
                    </div>
                </template>
                <template v-else>
                    <div v-for="(ch_page, index) in chapter.pages" :data-page="index+1" :style="'order: ' + (index+1) + ';'" :rendering="renderingMode"
                         class="reader-image-wrapper col-auto my-auto justify-content-center align-items-center noselect nodrag row no-gutters">
                    </div>
                </template>
            </div>
            <div id="banner_bottom" class="text-center"></div>
            <div id="reader-page-bar" class="col-auto d-none d-lg-flex directional">
                <div id="track" class="cursor-pointer row no-gutters">
                    <div id="trail" class="position-absolute h-100 noevents"
                         :style="'width: ' + (page === max_page ? 100*page/max_page : (100*(page+renderedPages-1)/max_page)) + '%;'">
                        <div id="thumb" class="h-100"
                             :style="'width: ' + (page === max_page ? 100/page : (100/(page+renderedPages-1)*renderedPages)) + '%;'"></div>
                    </div>
                    <div id="notches" class="row no-gutters h-100 w-100 directional">
                        <template v-for="(ch_page, index) in chapter.pages">
                            <router-link :class="images[index+1] == null ? 'notch col' : 'notch col loaded'"
                                         :data-page="index+1" @mouseover.native="setNotch(index+1)"
                                         :to="'#' + (index+1)" :style="'order: ' + (index+1) + ';'">
                                <div @click="needToRefresh = true"></div>
                            </router-link>
                        </template>
                    </div>
                    <div id="notch-display" class="col-auto m-auto px-3 py-1 noevents">
                        {{ hover_page }} / {{ max_page }}
                    </div>
                </div>
            </div>
        </div>
        <popup :popupData="popupData"></popup>
    </div>
    <div v-else ref="notfound"><popup :popupData="popupData"></popup></div>
</template>

<script>
import Vue from 'vue'
import {mapGetters} from "vuex";
import NotFound from './NotFound.vue';
import Popup from'./Popup.vue';

export default {
    name: "Read",
    components : { NotFound, Popup },
    mounted() {
        this.setupParams(this.$route.params)
        $('body').addClass('body-reader');
        $('#nav-search').show();
        $('#nav-filter').hide();
        if (this.hide_header) this.hideHeader();
        if (this.hide_sidebar) this.hideSidebar();
        this.$store.dispatch('fetchChapter', this.$route.path)
            .then(() => {
                if (this.$store.getters.chapter !== null) {
                    this.max_page = this.$store.getters.chapter.pages.length;
                    this.setPage(this.$route.hash);
                    this.updateViewChapter();
                } else {
                    const ComponentClass = Vue.extend(NotFound);
                    const instance = new ComponentClass();
                    instance.$mount();
                    if(typeof this.$refs.notfound !== "undefined") this.$refs.notfound.appendChild(instance.$el);
                    const title = 'Error 404' + " | " + this.reader.SITE_NAME;
                    $('title').html(title);
                    $('meta[property="og:title"]').html(title);
                }
            });
        this.chapter != null && this.updateCustomHTML();
    },
    beforeRouteUpdate(to, from, next) {
        if (from.path !== to.path) {
            this.setupParams(to.params)
            // If you don't set to null the Observer still has reference
            for (let i = 1; i <= this.max_page; i++) {
                this.images[i] = null;
            }
            this.needToRefresh = true;
            this.$store.dispatch('fetchChapter', to.path)
                .then(() => {
                    if (this.$store.getters.chapter !== null) {
                        this.max_page = this.$store.getters.chapter.pages.length;
                        this.setPage(to.hash);
                        this.updateViewChapter();
                    }
                });
        } else {
            this.animation = true;
            this.setPage(to.hash);
        }
        next();
    },
    updated() {
        if (this.needToRefresh) {
            if (this.renderingMode === 'long-strip') {
                for (let i = 1; i <= this.max_page; i++) {
                    $('.reader-image-wrapper[data-page="' + i + '"]')
                        .html(this.images[i] ? this.images[i] : null);
                }
            } else {
                $('.reader-image-wrapper[data-page="' + this.page + '"]').html(this.images[this.page]);
                if (this.renderingMode === 'double-page' && this.page < this.max_page) {
                    $('.reader-image-wrapper[data-page="' + (this.page + 1) + '"]')
                        .html(this.images[this.page + 1] ? this.images[this.page + 1] : null);
                }
                this.swipeInit();
            }
            this.scrollTopOfPage();
            this.needToRefresh = false;
        } else if (this.firstLoad && this.page > 1 && this.renderingMode === 'long-strip') {
            this.animation = true;
            this.scrollTopOfPage();
            this.firstLoad = false;
        }
        if(this.firstLoad && this.$store.getters.chapter && this.$store.getters.chapter.licensed) {
            this.showPopup();
        }

        if (this.chapter != null && (this.needToRefresh || this.firstLoad)) this.updateCustomHTML();
    },
    data() {
        return {
            reader: this.$root,
            viewed: this.$root.getSetting('viewed') ? JSON.parse('' + this.$root.getSetting('viewed')) : {},
            page: 1,
            max_page: 0,
            hover_page: 1,
            hide_header: parseInt(this.$root.getSetting('hide-header') || 0),
            hide_sidebar: parseInt(this.$root.getSetting('hide-sidebar') || 0),
            displayFit: this.$root.getSetting('displayFit') || 'fit-width',
            renderingMode: this.$root.getSetting('renderingMode') || 'single-page',
            direction: this.$root.getSetting('direction') || 'ltr',
            renderedPages: this.$root.getSetting('renderingMode') === 'double-page' ? 2 : 1,
            valueLeft: this.$root.getSetting('direction') === 'rtl' ? 1 : -1,
            valueRight: this.$root.getSetting('direction') === 'rtl' ? -1 : 1,
            animation: false,
            needToRefresh: true,
            images: [],
            firstLoad: true,
            popupData : {
                "header" : "Chapter licensed",
                "body" : "This chapter is licensed and you can't read it.",
                "button" : "Ok",
            }
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
        setPage(hash) {
            if (this.max_page < 1) return;
            if (hash === "#last" || hash === "") this.animation = false;
            let page = 1;
            let requested_page = hash.substring(1);
            if (requested_page === "last" || parseInt(requested_page) > this.max_page) {
                page = this.max_page;
            } else {
                page = isNaN(parseInt(requested_page)) || parseInt(requested_page) < 1 ? 1 : parseInt(requested_page);
            }
            this.page = page;
            let target_hash = '#' + page
            if (location.hash !== target_hash) {
                if (history.pushState) {
                    history.pushState(null, null, target_hash);
                } else {
                    location.hash = target_hash;
                }
            }
            this.preloadImagesFrom(page);
        },
        jumpPage(e) {
            this.needToRefresh = true;
            this.setPage('#' + e.target.value);
        },
        jumpChapter(e) {
            this.needToRefresh = true;
            this.$router.push(e.target.value);
        },
        setNotch(page) {
            this.hover_page = page;
        },
        hideHeader() {
            $('body').toggleClass('hide-header');
        },
        hideSidebar() {
            $('#reader').toggleClass('hide-sidebar');
        },
        toggleSettings() {
            $('#reader-controls-mode').toggleClass('d-none');
            $('#settings-button').toggleClass('active');
        },
        setSettings(e) {
            const setting = $(e.target).data('setting');
            const value = $(e.target).data('value');
            if (setting === 'direction') {
                this.direction = value;
                this.valueLeft = this.direction === 'ltr' ? -1 : 1;
                this.valueRight = this.direction === 'ltr' ? 1 : -1;
            } else if (setting === 'hide-header') {
                this.hide_header = value;
                this.hideHeader();
            } else if (setting === 'hide-sidebar') {
                this.hide_sidebar = value;
                this.hideSidebar();
            } else if (setting === 'displayFit') {
                this.displayFit = value;
            } else if (setting === 'renderingMode') {
                this.renderingMode = value;
                this.renderedPages = this.renderingMode === 'double-page' ? 2 : 1;
                this.needToRefresh = true;
            } else {
                return;
            }
            localStorage.setItem(setting, value);
            //this.reader.setCookie(setting, value, 3650);
        },
        scrollTopOfPage() {
            $('button[data-target="#navbarSupportedContent"][aria-expanded="true"]').click();
            if (this.max_page < 1 || !this.animation) return;
            let offset = $('body').hasClass('hide-header') ? 0 : Number(getComputedStyle(document.body, "").fontSize.match(/(\d*(\.\d*)?)px/)[1]) * 3.5
            if (this.renderingMode === 'long-strip') {
                canChange = false;
                $('html,body').animate({scrollTop: $('.reader-image-wrapper[data-page="' + this.page + '"]').offset().top - offset}, 400, function () {
                    canChange = true;
                });
            } else {
                $('html,body').scrollTop($('.reader-image-wrapper[data-page="' + this.page + '"]').offset().top - offset);
            }
            this.animation = false;
        },
        preloadImage(url, i) {
            return new Promise((resolve, reject) => {
                this.images[i] = new Image();
                this.images[i].onload = function () {
                    resolve(url)
                }
                this.images[i].onerror = function () {
                    reject(url)
                }

                this.images[i].draggable = "false";
                this.images[i].className = "noselect nodrag cursor-pointer";
                this.images[i].alt = "Page " + i;
                this.images[i].src = url;
            })
        },
        recursivePreload(page_number) {
            if (this.images[page_number] == null) {
                $('.reader-image-wrapper[data-page="' + page_number + '"]').html('');
                this.preloadImage(this.$store.getters.chapter.pages[page_number - 1], page_number).then((successUrl) => {
                    $('.notch[data-page="' + page_number + '"]').addClass('loaded');
                    $('.reader-image-wrapper[data-page="' + page_number + '"]').html(this.images[page_number]);
                    if (page_number < this.max_page) {
                        this.recursivePreload(page_number + 1, this.max_page);
                    }
                }).catch((errorUrl) => {
                    // Nothing
                });
            }
        },
        async preloadImagesFrom(page_number) {
            if (page_number == null) return;
            this.recursivePreload(page_number);
        },
        clickPage(e){
            const pW = document.body.clientWidth - document.body.scrollWidth + $(e.target).closest('#reader-images').width();
            e.clientX < pW / 2 ? $('#turn-left').click() : $('#turn-right').click();
        },
        swipeInit() {
            $(document).ready(function(){
                $('#reader-images').swipe( {
                    swipeLeft: function() {$('#reader-images:not([rendering=long-strip])').length && $('html,body').scrollTop() > 200 && $('#turn-right').click()},
                    swipeRight: function() {$('#reader-images:not([rendering=long-strip])').length && $('html,body').scrollTop() > 200 && $('#turn-left').click()},
                });
            });
        },
        updateViewChapter() {
            const comic = this.$store.getters.comic;
            const chapter = this.$store.getters.chapter;
            if(this.max_page > 0) {
                if(typeof this.viewed[comic.slug] === 'undefined'){
                    this.viewed[comic.slug] = {};
                }
                this.viewed[comic.slug][chapter.slug_lang_vol_ch_sub] = 1;
                localStorage.setItem('viewed', JSON.stringify(this.viewed));
                //this.reader.setCookie('viewed', JSON.stringify(this.viewed), 3650);
            }
            const title = chapter.full_title + " | " + comic.title + " | " + this.reader.SITE_NAME;
            $('title').html(title);
            $('meta[property="og:title"]').html(title);
            this.firstLoad = true;
            this.$store.dispatch('fetchVote', chapter.vote_id);
        },
        sendVote(e) {
            const vote = parseInt($(e.target).data('vote'));
            $('.ratings .star.selected').each(function(){
                $(this).removeClass('selected');
            });
            $(e.target).addClass('selected');
            $.ajax({
                type: 'POST',
                url: this.reader.API_BASE_URL + '/vote' + this.$route.path.slice(5),
                headers: {
                    'X-CSRF-TOKEN': this.$store.getters.csrf_token,
                },
                data: {'vote': vote},
                error: function (err) {
                    console.log(err);
                },
            });
        },
        showPopup() {
            $('#modal-container').modal({show: true, closeOnEscape: true, backdrop: 'static', keyboard: true});
        },
        updateCustomHTML() {
            this.reader.updateCustomHTML('banner_top');
            this.reader.updateCustomHTML('banner_bottom');
            this.reader.updateCustomHTML('reader_html');
        },
    },
    computed: {
        ...mapGetters([
            'comic',
            'chapter',
            'vote',
            'csrf_token'
        ])
    }
}
</script>
