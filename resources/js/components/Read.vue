<!--suppress XmlDuplicatedId -->
<template>
    <div v-if="max_page > 0" id="read"
         :class="'reader row flex-column no-gutters layout-horizontal' + (hide_sidebar ? ' hide-sidebar' : '')"
         :data-renderer="renderingMode" :data-display="displayFit" :data-direction="direction">
        <div class="container reader-controls-container">
            <div class="reader-controls-wrapper bg-reader-controls row no-gutters flex-nowrap" style="z-index:1">
                <div class="d-none d-lg-flex col-auto justify-content-center align-items-center cursor-pointer"
                     id="reader-controls-collapser" data-setting="hide-sidebar" :data-value="~hide_sidebar+2"
                     @click="setSetting">
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
                                     class="chapter-link-right col-auto arrow-link" :to="chapter.next.url">
                            <span class="fas fa-angle-right fa-fw" aria-hidden="true"></span>
                        </router-link>
                        <router-link v-else class="chapter-link-right col-auto arrow-link"
                                     title="Back to comic" :to="comic.url">
                            <span class="fas fa-angle-right fa-fw" aria-hidden="true"></span>
                        </router-link>
                        <div class="col-auto py-2 pr-2 d-lg-none">
                            <select class="form-control" id="jump-page" name="jump-page">
                                <template v-for="(ch_page, index) in chapter.pages">
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
                            <a title="Hide header" class="btn btn-secondary col m-1" role="button" @click="setSetting"
                               id="hide-header-button" :data-value="~hide_header+2" data-setting="hide-header">
                                <span class="far fa-window-maximize fa-fw" :data-value="~hide_header+2"
                                      data-setting="hide-header"></span>
                                <span class="d-none d-lg-inline" :data-value="~hide_header+2"
                                      data-setting="hide-header"> Hide header</span>
                            </a>
                        </div>
                    </div>
                    <div
                        class="reader-controls-mode col-auto d-lg-flex d-none flex-column align-items-start row no-gutters p-2">
                        <div class="col text-center font-weight-bold">Fit display to</div>
                        <div class="col btn-group btn-group-toggle" data-toggle="buttons">
                            <label v-if="displayFit === 'fit-both'" class="btn btn-secondary col-3 active">
                                <input type="radio" data-value="fit-both" data-setting="displayFit" @click="setSetting"
                                       autocomplete="off" onkeydown="event.preventDefault()" checked>Both
                            </label>
                            <label v-else class="btn btn-secondary col-3">
                                <input type="radio" data-value="fit-both" data-setting="displayFit" @click="setSetting"
                                       autocomplete="off" onkeydown="event.preventDefault()">Both
                            </label>
                            <label v-if="displayFit === 'fit-width'" class="btn btn-secondary col-3 active">
                                <input type="radio" data-value="fit-width" data-setting="displayFit" @click="setSetting"
                                       autocomplete="off" onkeydown="event.preventDefault()" checked>Width
                            </label>
                            <label v-else class="btn btn-secondary col-3">
                                <input type="radio" data-value="fit-width" data-setting="displayFit" @click="setSetting"
                                       autocomplete="off" onkeydown="event.preventDefault()">Width
                            </label>
                            <label v-if="displayFit === 'fit-height'" class="btn btn-secondary col-3 active">
                                <input type="radio" data-value="fit-height" data-setting="displayFit"
                                       @click="setSetting"
                                       autocomplete="off" onkeydown="event.preventDefault()" checked>Height
                            </label>
                            <label v-else class="btn btn-secondary col-3">
                                <input type="radio" data-value="fit-height" data-setting="displayFit"
                                       @click="setSetting"
                                       autocomplete="off" onkeydown="event.preventDefault()">Height
                            </label>
                            <label v-if="displayFit === 'no-resize'" class="btn btn-secondary col-3 active">
                                <input type="radio" data-value="no-resize" data-setting="displayFit" @click="setSetting"
                                       autocomplete="off" onkeydown="event.preventDefault()" checked>Full
                            </label>
                            <label v-else class="btn btn-secondary col-3">
                                <input type="radio" data-value="no-resize" data-setting="displayFit" @click="setSetting"
                                       autocomplete="off" onkeydown="event.preventDefault()">Full
                            </label>
                        </div>
                        <div class="col text-center font-weight-bold mt-2">Page rendering</div>
                        <div class="col btn-group btn-group-toggle" data-toggle="buttons">
                            <label v-if="renderingMode === 'double-page'" class="btn btn-secondary col-4 active">
                                <input type="radio" data-value="double-page" data-setting="renderingMode"
                                       @click="setSetting"
                                       autocomplete="off" onkeydown="event.preventDefault()" checked>Double
                            </label>
                            <label v-else class="btn btn-secondary col-4">
                                <input type="radio" data-value="double-page" data-setting="renderingMode"
                                       @click="setSetting"
                                       autocomplete="off" onkeydown="event.preventDefault()">Double
                            </label>
                            <label v-if="renderingMode === 'single-page'" class="btn btn-secondary col-4 active">
                                <input type="radio" data-value="single-page" data-setting="renderingMode"
                                       @click="setSetting"
                                       autocomplete="off" onkeydown="event.preventDefault()" checked>Single
                            </label>
                            <label v-else class="btn btn-secondary col-4">
                                <input type="radio" data-value="single-page" data-setting="renderingMode"
                                       @click="setSetting"
                                       autocomplete="off" onkeydown="event.preventDefault()">Single
                            </label>
                            <label v-if="renderingMode === 'long-strip'" class="btn btn-secondary col-4 active">
                                <input type="radio" data-value="long-strip" data-setting="renderingMode"
                                       @click="setSetting"
                                       autocomplete="off" onkeydown="event.preventDefault()" checked>Long Strip
                            </label>
                            <label v-else class="btn btn-secondary col-4">
                                <input type="radio" data-value="long-strip" data-setting="renderingMode"
                                       @click="setSetting"
                                       autocomplete="off" onkeydown="event.preventDefault()">Long Strip
                            </label>
                        </div>
                        <div class="col text-center font-weight-bold mt-2">Direction</div>
                        <div class="col btn-group btn-group-toggle" data-toggle="buttons">
                            <label v-if="direction === 'ltr'" class="btn btn-secondary col-6 active">
                                <input type="radio" data-value="ltr" data-setting="direction" @click="setSetting"
                                       autocomplete="off" onkeydown="event.preventDefault()" checked>Left to right
                            </label>
                            <label v-else class="btn btn-secondary col-6">
                                <input type="radio" data-value="ltr" data-setting="direction" @click="setSetting"
                                       autocomplete="off" onkeydown="event.preventDefault()">Left to right
                            </label>
                            <label v-if="direction === 'rtl'" class="btn btn-secondary col-6 active">
                                <input type="radio" data-value="rtl" data-setting="direction" @click="setSetting"
                                       autocomplete="off" onkeydown="event.preventDefault()" checked>Right to left
                            </label>
                            <label v-else class="btn btn-secondary col-6">
                                <input type="radio" data-value="rtl" data-setting="direction" @click="setSetting"
                                       autocomplete="off" onkeydown="event.preventDefault()">Right to left
                            </label>
                        </div>
                    </div>
                    <div class="reader-controls-footer col-auto mt-auto d-none d-lg-flex justify-content-center">
                        <div
                            class="text-muted text-center text-truncate row flex-wrap justify-content-center p-2 no-gutters">
                            Powered by&nbsp;
                            <a href="https://github.com/FedericoHeichou/PizzaReader" target="_blank">
                                PizzaReader
                            </a>
                        </div>
                    </div>
                    <div class="reader-controls-pages col-auto d-none d-lg-flex row no-gutters align-items-center">
                        <router-link v-if="(valueLeft === -1 && page > 1) ||
                        (valueLeft === 1 && page < max_page)" :to="'#' + (page+valueLeft)"
                                     class="page-link-left col-auto arrow-link">
                            <span class="fas fa-angle-left fa-fw" aria-hidden="true" title="Turn page left"
                                  @click="needToRefresh = true" id="turn-left"></span>
                        </router-link>
                        <router-link v-else-if="chapter.previous != null" :to="chapter.previous.url + '#last'"
                                     class="page-link-left col-auto arrow-link">
                            <span class="fas fa-angle-left fa-fw" aria-hidden="true" title="Turn page left"
                                  @click="needToRefresh = true" id="turn-left"></span>
                        </router-link>
                        <router-link v-else :to="comic.url"
                                     class="page-link-left col-auto arrow-link">
                            <span class="fas fa-angle-left fa-fw" aria-hidden="true" title="Turn page left"
                                  @click="needToRefresh = true" id="turn-left"></span>
                        </router-link>

                        <div class="col text-center reader-controls-page-text cursor-pointer">
                            Page <span class="current-page">{{ page }}</span> /
                            <span class="total-pages"> {{ max_page }} </span>
                        </div>

                        <router-link v-if="(valueRight === 1 && page < max_page) ||
                                    (valueRight === -1 && page > 1)" :to="'#' + (page+valueRight)"
                                     class="page-link-right col-auto arrow-link">
                            <span class="fas fa-angle-right fa-fw" aria-hidden="true" title="Turn page right"
                                  @click="needToRefresh = true" id="turn-right"></span>
                        </router-link>
                        <router-link v-else-if="chapter.next != null" :to="chapter.next.url"
                                     class="page-link-right col-auto arrow-link">
                            <span class="fas fa-angle-right fa-fw" aria-hidden="true" title="Turn page right"
                                  @click="needToRefresh = true" id="turn-right"></span>
                        </router-link>
                        <router-link v-else :to="comic.url"
                                     class="page-link-right col-auto arrow-link">
                            <span class="fas fa-angle-right fa-fw" aria-hidden="true" title="Turn page right"
                                  @click="needToRefresh = true" id="turn-right"></span>
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
            <div
                class="reader-images col-auto row no-gutters flex-nowrap m-auto text-center cursor-pointer directional">
                <template v-if="renderingMode === 'single-page'">
                    <div :data-page="page" :style="'order: ' + page + ';'"
                         class="reader-image-wrapper col-auto my-auto justify-content-center align-items-center noselect nodrag row no-gutters">
                    </div>
                </template>
                <template v-else-if="renderingMode === 'double-page'">
                    <div :data-page="page" :style="'order: ' + page + ';'"
                         class="reader-image-wrapper col-auto my-auto justify-content-center align-items-center noselect nodrag row no-gutters">
                    </div>
                    <div v-if="chapter.pages[page] != null" :data-page="page+1" :style="'order: ' + (page+1) + ';'"
                         class="reader-image-wrapper col-auto my-auto justify-content-center align-items-center noselect nodrag row no-gutters">
                    </div>
                </template>
                <template v-else>
                    <div v-for="(ch_page, index) in chapter.pages" :data-page="index+1"
                         :style="'order: ' + (index+1) + ';'"
                         class="reader-image-wrapper col-auto my-auto justify-content-center align-items-center noselect nodrag row no-gutters">
                    </div>
                </template>
            </div>
            <div class="reader-load-icon">
                <span class="fas fa-circle-notch fa-spin" aria-hidden="true"></span>
            </div>
            <div class="reader-page-bar col-auto d-none d-lg-flex directional">
                <div class="track cursor-pointer row no-gutters">
                    <div class="trail position-absolute h-100 noevents"
                         :style="'width: ' + ((page+renderedPages-1)/max_page*100) + '%;'">
                        <div class="thumb h-100"
                             :style="'width: calc(' + (100/page*renderedPages) + '% - ' + (renderedPages*3-2) +'px);'"></div>
                    </div>
                    <div class="notches row no-gutters h-100 w-100 directional">
                        <template v-for="(ch_page, index) in chapter.pages">
                            <router-link :class="images[index+1] == null ? 'notch col' : 'notch col loaded'"
                                         :data-page="index+1" @mouseover.native="setNotch(index+1)"
                                         :to="'#' + (index+1)" :style="'order: ' + (index+1) + ';'">
                                <div @click="needToRefresh = true"></div>
                            </router-link>
                        </template>
                    </div>
                    <div class="notch-display col-auto m-auto px-3 py-1 noevents">
                        {{ hover_page }} / {{ max_page }}
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
        if (this.hide_header) this.hideHeader();
        this.$store.dispatch('fetchChapter', this.$route.path)
            .then(() => {
                if (this.$store.getters.chapter != null) {
                    this.max_page = this.$store.getters.chapter.pages.length;
                    this.setPage(this.$route.hash);
                }
            });
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
                    if (this.$store.getters.chapter != null) {
                        this.max_page = this.$store.getters.chapter.pages.length;
                        this.setPage(to.hash);
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
            }
            this.scrollTopOfPage();
            this.needToRefresh = false;
        }
    },
    data() {
        return {
            page: 1,
            max_page: 0,
            hover_page: 1,
            hide_header: parseInt(this.getCookie('hide-header') || 0),
            hide_sidebar: parseInt(this.getCookie('hide-sidebar') || 0),
            displayFit: this.getCookie('displayFit') || 'fit-width',
            renderingMode: this.getCookie('renderingMode') || 'single-page',
            direction: this.getCookie('direction') || 'ltr',
            renderedPages: 1,
            valueLeft: this.getCookie('direction') === 'ltr' ? -1 : 1,
            valueRight: this.getCookie('direction') === 'ltr' ? 1 : -1,
            animation: false,
            needToRefresh: true,
            images: [],
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
            if (location.hash !== '#' + page) {
                if (history.pushState) {
                    history.pushState(null, null, '#' + page);
                } else {
                    location.hash = '#' + page;
                }
            }
            this.preloadImagesFrom(page);
        },
        setNotch(page) {
            this.hover_page = page;
        },
        hideHeader() {
            $('body').toggleClass('hide-header');
        },
        setSetting(e) {
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
            } else if (setting === 'displayFit') {
                this.displayFit = value;
            } else if (setting === 'renderingMode') {
                this.renderingMode = value;
                this.renderedPages = this.renderingMode === 'double-page' ? 2 : 1;
                this.needToRefresh = true;
            }

            this.setCookie(setting, value, 1);
        },
        scrollTopOfPage() {
            if (this.max_page < 1) return;
            if (this.renderingMode === 'long-strip') {
                let offset = $('body').hasClass('hide-header') ? 0 : Number(getComputedStyle(document.body, "").fontSize.match(/(\d*(\.\d*)?)px/)[1]) * 3.5
                if (!this.animation) {
                    $('html,body').scrollTop($('.reader-image-wrapper[data-page="' + this.page + '"]').offset().top - offset);
                } else {
                    canChange = false;
                    $('html,body').animate({scrollTop: $('.reader-image-wrapper[data-page="' + this.page + '"]').offset().top - offset}, 400, function () {
                        canChange = true;
                    });
                }
            } else {
                $('html,body').scrollTop(0);
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
                        this.recursivePreload(page_number + 1, this.max_page)
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
        setCookie(cname, cvalue, exdays) {
            let d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            let expires = "expires=" + d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        },
        getCookie(cname) {
            let name = cname + "=";
            let ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) === 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }
    },
    computed: {
        ...mapGetters([
            'comic',
            'chapter',
        ])
    }
}
</script>
