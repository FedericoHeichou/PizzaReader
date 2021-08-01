/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Vue from 'vue';

import store from './store/index.js';
import router from './router/index.js';

const app = new Vue({
    el: '#app',
    router,
    store,
    data() {
        return {
            BASE_URL: typeof BASE_URL !== 'undefined' ? BASE_URL : '/',
            API_BASE_URL: typeof API_BASE_URL !== 'undefined' ? API_BASE_URL : '/api',
            SITE_NAME: typeof SITE_NAME !== 'undefined' ? SITE_NAME : 'PizzaReader',
            SITE_NAME_FULL: typeof SITE_NAME_FULL !== 'undefined' ? SITE_NAME_FULL : 'PizzaReader - Read manga online',
            homepage_html: null,
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
    }
});
