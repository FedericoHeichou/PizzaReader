import Vue from 'vue'
import VueRouter from 'vue-router'
import Comics from "../components/Comics";
import Comic from "../components/Comic";
import Read from "../components/Read";
import Target from "../components/Target";
import Genre from "../components/Genre";

Vue.use(VueRouter)

const router = new VueRouter({
    mode: 'history',
    base: __dirname,
    routes: [
        {path: '/', redirect: '/comics'},
        {path: '/public/*', redirect: '/*'},
        {path: '/comics', component: Comics},
        {path: '/comics/:slug', component: Comic},
        {path: '/targets/:target', component: Target},
        {path: '/genres/:genre', component: Genre},
        {path: '/read/:slug/:lang/(vol/\\d+)?/(ch/\\d+)?/(sub/\\d+)?', component: Read},
    ]
})

export default router
