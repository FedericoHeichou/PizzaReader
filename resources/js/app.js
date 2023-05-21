import './bootstrap';

import { createApp } from 'vue';

import { store } from './store/index.js';
import { router } from './router/index.js';

const app = createApp({
    data() {
        return {
            BASE_URL: typeof BASE_URL !== 'undefined' ? BASE_URL : '/',
            API_BASE_URL: typeof API_BASE_URL !== 'undefined' ? API_BASE_URL : '/api',
            SITE_NAME: typeof SITE_NAME !== 'undefined' ? SITE_NAME : 'PizzaReader',
            SITE_NAME_FULL: typeof SITE_NAME_FULL !== 'undefined' ? SITE_NAME_FULL : 'PizzaReader - Read manga online',
            custom_html: {
                'homepage_html': null,
                'all_comics_top_html': null,
                'all_comics_bottom_html': null,
                'comic_top_html': null,
                'comic_bottom_html': null,
                'reader_html': null,
                'banner_top': null,
                'banner_bottom': null,
            },
            custom_html_observer: {},
        }
    },
    methods: {
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
        },
        getSetting(setting) {
            // Legacy compatibility
            const val = localStorage.getItem(setting);
            if (val !== null) return val;
            const cookie = this.getCookie(setting);
            if (cookie !== '') {
                localStorage.setItem(setting, cookie);
                this.setCookie(setting, cookie, -1);
            }
            return cookie;
        },
        __(message) { return typeof lang_messages !== 'undefined' && lang_messages[message] || message; },
        updateCustomHTML(id) {
            const custom_html_selector = $(`#${id}`);
            if (this.custom_html[id] === null) {
                custom_html_selector.html(custom_html_placeholder[id]);
                this.custom_html_observer[id] = this.observeSelector(id);
            } else {
                custom_html_selector.html(this.custom_html[id]);
                //this.custom_html_observer[id].observe();
            }
        },
        observeSelector(id) {
            const targetNode = document.querySelector(`#${id}`);
            if (targetNode) {
                const config = {attributes: true, childList: true, subtree: true};
                const reader = this;
                const callback = function (mutationsList, observer) {
                    reader.custom_html[id] = targetNode.innerHTML;
                };
                const observer = new MutationObserver(callback);
                observer.observe(targetNode, config);
                return observer;
            }
        },
        clearCustomHTML(id) {
            const custom_html_selector = $(`#${id}`);
            if (custom_html_selector.length && typeof this.custom_html_observer[id] !== 'undefined') {
                this.custom_html_observer[id].disconnect();
                custom_html_selector.html('');
            }
        },
    },
});
app.use(store);
app.use(router);
app.mount('#app');
