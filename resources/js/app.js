/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import VueRouter from 'vue-router'

Vue.use(VueRouter)

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
// Vue.component('reader', require('./components/Reader.vue').default);

import Comics from './components/Comics.vue';
import Comic from './components/Comic.vue';
import Read from './components/Read.vue';

const router = new VueRouter({
    mode: 'history',
    base: __dirname,
    routes: [
        { path: '/', redirect: '/comics' },
        { path: '/public/*', redirect: '/*' },
        { path: '/comics', component: Comics },
        { path: '/comics/:stub', component: Comic },
        { path: '/read/:stub/:lang/vol/:vol/ch/:ch/sub/:sub', component: Read }, // Yandere-dev mode
        { path: '/read/:stub/:lang/vol/:vol/ch/:ch', component: Read },
        { path: '/read/:stub/:lang/vol/:vol/sub/:sub', component: Read },
        { path: '/read/:stub/:lang/ch/:ch/sub/:sub', component: Read },
        { path: '/read/:stub/:lang/vol/:vol', component: Read },
        { path: '/read/:stub/:lang/ch/:ch', component: Read },
        { path: '/read/:stub/:lang/sub/:sub', component: Read },
        { path: '/read/:stub/:lang/', component: Read },
    ]
})

new Vue({
    el: '#app',
    router
});
