<template>
    <div v-if="chapter != null && comic != null && comic.url != null && chapter.pages.length > 0" id="read"
         class="reader row flex-column no-gutters layout-horizontal fit-horizontal">
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
                                <template v-for="(page, index) in chapter.pages">
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
                            <a title="Hide header" class="btn btn-secondary col m-1" role="button" id="hide-header-button">
                                <span class="far fa-window-maximize fa-fw"></span>
                                <span class="d-none d-lg-inline"> Hide header</span>
                            </a>
                        </div>
                    </div>
                    <div class="reader-controls-mode col-auto d-lg-flex d-none flex-column align-items-start row no-gutters p-2">
                        <div class="col text-center font-weight-bold">Fit display to</div>
                        <div class="col btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-secondary col-4">
                                <input type="radio" data-value="container" data-setting="displayFit"
                                       autocomplete="off">Container
                            </label>
                            <label class="btn btn-secondary col-4 active">
                                <input type="radio" data-value="width" data-setting="displayFit"
                                       autocomplete="off" checked>Width
                            </label>
                            <label class="btn btn-secondary col-4">
                                <input type="radio" data-value="height" data-setting="displayFit"
                                       autocomplete="off">Height
                            </label>
                        </div>
                        <div class="col text-center font-weight-bold mt-2">Page rendering</div>
                        <div class="col btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-secondary col-4">
                                <input type="radio" data-value="double" data-setting="renderingMode" autocomplete="off">Double
                            </label>
                            <template v-if="chapter.format_id === 2">
                                <label class="btn btn-secondary col-4">
                                    <input type="radio" data-value="single" data-setting="renderingMode"
                                           autocomplete="off">Single
                                </label>
                                <label class="btn btn-secondary col-4 active">
                                    <input type="radio" data-value="long-strip" data-setting="renderingMode"
                                           autocomplete="off" checked>Long Strip
                                </label>
                            </template>
                            <template v-else>
                                <label class="btn btn-secondary col-4 active">
                                    <input type="radio" data-value="single" data-setting="renderingMode"
                                           autocomplete="off" checked>Single
                                </label>
                                <label class="btn btn-secondary col-4">
                                    <input type="radio" data-value="long-strip" data-setting="renderingMode"
                                           autocomplete="off">Long Strip
                                </label>
                            </template>
                        </div>
                        <div class="col text-center font-weight-bold mt-2">Direction</div>
                        <div class="col btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-secondary col-6 active">
                                <input type="radio" data-value="left-to-right" data-setting="direction"
                                       autocomplete="off" checked>Left to right
                            </label>
                            <label class="btn btn-secondary col-6">
                                <input type="radio" data-value="right-to-left" data-setting="direction"
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
                                     class="page-link-left col-auto arrow-link"
                                     data-action="page" data-direction="left" data-by="1">
                            <span class="fas fa-angle-left fa-fw" aria-hidden="true" title="Turn page left"></span>
                        </router-link>
                        <router-link v-else-if="chapter.previous != null" :to="chapter.previous.url + '#last'"
                                     class="page-link-left col-auto arrow-link"
                                     data-action="page" data-direction="left" data-by="1">
                            <span class="fas fa-angle-left fa-fw" aria-hidden="true" title="Turn page left"></span>
                        </router-link>
                        <router-link v-else :to="comic.url" class="page-link-left col-auto arrow-link"
                                     data-action="page" data-direction="left" data-by="1">
                            <span class="fas fa-angle-left fa-fw" aria-hidden="true" title="Turn page left"></span>
                        </router-link>

                        <div class="col text-center reader-controls-page-text cursor-pointer">
                            Page <span class="current-page">{{ page }}</span> / <span class="total-pages">{{ chapter.pages.length }}</span>
                        </div>

                        <router-link v-if="(valueRight === 1 && page < chapter.pages.length) ||
                                    (valueRight === -1 && page > 1)" :to="'#' + (page+valueRight)"
                                     class="page-link-right col-auto arrow-link"
                                     data-action="page" data-direction="right" data-by="1">
                            <span class="fas fa-angle-right fa-fw" aria-hidden="true" title="Turn page right"></span>
                        </router-link>
                        <router-link v-else-if="chapter.next != null" :to="chapter.next.url"
                                     class="page-link-right col-auto arrow-link"
                                     data-action="page" data-direction="left" data-by="1">
                            <span class="fas fa-angle-right fa-fw" aria-hidden="true" title="Turn page right"></span>
                        </router-link>
                        <router-link v-else :to="comic.url" class="page-link-right col-auto arrow-link"
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
            <div class="reader-goto-top d-flex d-lg-none justify-content-center align-items-center fade cursor-pointer" data-scroll="0" data-turn="0" data-threshold="100">
                <span class="fas fa-angle-up"></span>
            </div>
            <div class="reader-images col-auto row no-gutters flex-nowrap m-auto text-center cursor-pointer directional"><div class="reader-image-wrapper col-auto my-auto justify-content-center align-items-center noselect nodrag row no-gutters" data-state="2" data-page="25" style="order: 25;"><img draggable="false" class="noselect nodrag cursor-pointer" src="https://s9q5t4xxwkqyt.n89szn4j53mg0.mangadex.network/GfoMdQwAojRhAd1rXFbpZu6WFqJuVmaarIgxEegNYA1qV52gFkW-FDaAuR1QLdRZD7kVfysTs-A9cXQQtg7VS5QJZauCeDssGJq42WVbcmtaVypQ5RBEwmWx0oY56oTE3gHGUQVDzSNwRrwWFif-hKFNjaW33KPu-zNPgCjpkvHxReKKrPo45sLz6GERNemlT3f1hFTt0xKDhfIR6fzoQ_5v/data-saver/626e6405f890735f4c985e32c2c3631e/s25.png"></div></div>
            <div class="reader-load-icon">
                <span class="fas fa-circle-notch fa-spin" aria-hidden="true"></span>
            </div>
            <div class="reader-page-bar col-auto d-none d-lg-flex directional">
                <div class="track cursor-pointer row no-gutters">
                    <div class="trail position-absolute h-100 noevents" style="width: 46.2963%;">
                        <div class="thumb h-100" style="width: 4%; float: right;"></div>
                    </div>
                    <div class="notches row no-gutters h-100 w-100 directional"><div class="notch col loaded" data-page="1" style="order: 1;"></div><div class="notch col loaded" data-page="2" style="order: 2;"></div><div class="notch col loaded" data-page="3" style="order: 3;"></div><div class="notch col loaded" data-page="4" style="order: 4;"></div><div class="notch col loaded" data-page="5" style="order: 5;"></div><div class="notch col loaded" data-page="6" style="order: 6;"></div><div class="notch col loaded" data-page="7" style="order: 7;"></div><div class="notch col loaded" data-page="8" style="order: 8;"></div><div class="notch col loaded" data-page="9" style="order: 9;"></div><div class="notch col loaded" data-page="10" style="order: 10;"></div><div class="notch col loaded" data-page="11" style="order: 11;"></div><div class="notch col loaded" data-page="12" style="order: 12;"></div><div class="notch col loaded" data-page="13" style="order: 13;"></div><div class="notch col loaded" data-page="14" style="order: 14;"></div><div class="notch col loaded" data-page="15" style="order: 15;"></div><div class="notch col loaded" data-page="16" style="order: 16;"></div><div class="notch col loaded" data-page="17" style="order: 17;"></div><div class="notch col loaded" data-page="18" style="order: 18;"></div><div class="notch col loaded" data-page="19" style="order: 19;"></div><div class="notch col loaded" data-page="20" style="order: 20;"></div><div class="notch col loaded" data-page="21" style="order: 21;"></div><div class="notch col loaded" data-page="22" style="order: 22;"></div><div class="notch col loaded" data-page="23" style="order: 23;"></div><div class="notch col loaded" data-page="24" style="order: 24;"></div><div class="notch col loaded" data-page="25" style="order: 25;"></div><div class="notch col loaded" data-page="26" style="order: 26;"></div><div class="notch col loaded" data-page="27" style="order: 27;"></div><div class="notch col loaded" data-page="28" style="order: 28;"></div><div class="notch col loaded" data-page="29" style="order: 29;"></div><div class="notch col loaded" data-page="30" style="order: 30;"></div><div class="notch col loaded" data-page="31" style="order: 31;"></div><div class="notch col loaded" data-page="32" style="order: 32;"></div><div class="notch col loaded" data-page="33" style="order: 33;"></div><div class="notch col loaded" data-page="34" style="order: 34;"></div><div class="notch col loaded" data-page="35" style="order: 35;"></div><div class="notch col loaded" data-page="36" style="order: 36;"></div><div class="notch col loaded" data-page="37" style="order: 37;"></div><div class="notch col loaded" data-page="38" style="order: 38;"></div><div class="notch col loaded" data-page="39" style="order: 39;"></div><div class="notch col loaded" data-page="40" style="order: 40;"></div><div class="notch col loaded" data-page="41" style="order: 41;"></div><div class="notch col loaded" data-page="42" style="order: 42;"></div><div class="notch col loaded" data-page="43" style="order: 43;"></div><div class="notch col loaded" data-page="44" style="order: 44;"></div><div class="notch col loaded" data-page="45" style="order: 45;"></div><div class="notch col loaded" data-page="46" style="order: 46;"></div><div class="notch col loaded" data-page="47" style="order: 47;"></div><div class="notch col loaded" data-page="48" style="order: 48;"></div><div class="notch col loaded" data-page="49" style="order: 49;"></div><div class="notch col loaded" data-page="50" style="order: 50;"></div><div class="notch col loaded" data-page="51" style="order: 51;"></div><div class="notch col loaded" data-page="52" style="order: 52;"></div><div class="notch col loaded" data-page="53" style="order: 53;"></div><div class="notch col loaded" data-page="54" style="order: 54;"></div></div>
                    <div class="notch-display col-auto m-auto px-3 py-1 noevents">28 / 54</div>
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
        $('#header').addClass('header-fixed');
        $('#nav-search').show();
        $('#nav-filter').hide();

        this.$store.dispatch('fetchChapter', this.$route);
    },
    beforeRouteUpdate(to, from, next) {
        if (to.params.pathMatch) {
            to.params.vol = to.params.pathMatch.split("/")[1];
        }
        delete to.params.pathMatch;
        if (to.params[1]) {
            to.params.ch = to.params[1].split("/")[1];
        }
        delete to.params[1];
        if (to.params[2]) {
            to.params.sub = to.params[2].split("/")[1];
        }
        delete to.params[2];
        if(to.params.vol !== from.params.vol || to.params.ch !== from.params.ch || to.params.sub !== from.params.sub) {
            this.$store.dispatch('fetchChapter', to);
        } else {
            let page = 1;
            let hash = to.hash.substring(1);
            if(hash === "last" || parseInt(hash) > this.$store.getters.chapter.pages.length) {
                page = this.$store.getters.chapter.pages.length;
            }else {
                page = isNaN(parseInt(hash)) || parseInt(hash) < 1 ? 1 : parseInt(hash);
            }
            this.$store.commit('setPage', page);
            if(history.pushState) {
                history.pushState(null, null, '#' + page);
            } else {
                location.hash = '#' + page;
            }
        }
        next();
    },
    methods: {},
    computed: {
        ...mapGetters([
            'comic',
            'chapter',
            'page',
            'valueLeft',
            'valueRight',
        ])
    }
}
</script>
