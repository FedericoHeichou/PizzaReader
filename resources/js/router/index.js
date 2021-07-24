import Vue from 'vue'
import VueRouter from 'vue-router'
import Comics from "../components/Comics.vue";
import Comic from "../components/Comic.vue";
import Read from "../components/Read.vue";
import NotFound from "../components/NotFound.vue";

Vue.use(VueRouter)

const router = new VueRouter({
    mode: 'history',
    base: __dirname,
    routes: [
        {path: '/public/*', redirect: '/*'},
        {path: '/', component: Comics, name: 'Last Releases'},
        {path: '/comics', component: Comics, name: 'All Comics'},
        {path: '/recommended', component: Comics, name: 'Recommended'},
        {path: '/comics/:slug', component: Comic},
        {path: '/targets/:target', component: Comics},
        {path: '/genres/:genre', component: Comics},
        {path: '/read/:slug/:lang/(vol/\\d+)?/(ch/\\d+)?/(sub/\\d+)?', component: Read},
        {path: '*', component: NotFound},
    ]
})

export default router
