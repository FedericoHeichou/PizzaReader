import Vue from 'vue'
import VueRouter from 'vue-router'
import Comics from "../components/Comics";
import Comic from "../components/Comic";
import Read from "../components/Read";
import NotFound from "../components/NotFound";

Vue.use(VueRouter)

const router = new VueRouter({
    mode: 'history',
    base: __dirname,
    routes: [
        {path: '/', redirect: '/comics'},
        {path: '/public/*', redirect: '/*'},
        {path: '/comics', component: Comics},
        {path: '/alph', component: Comics},
        {path: '/comics/:slug', component: Comic},
        {path: '/targets/:target', component: Comics},
        {path: '/genres/:genre', component: Comics},
        {path: '/read/:slug/:lang/(vol/\\d+)?/(ch/\\d+)?/(sub/\\d+)?', component: Read},
        {path: '*', component: NotFound},
    ]
})

export default router
