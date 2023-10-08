import { createRouter, createWebHistory } from 'vue-router'
import Comics from "../components/Comics.vue";
import Comic from "../components/Comic.vue";
import Read from "../components/Read.vue";
import NotFound from "../components/NotFound.vue";

export const router = createRouter({
    history: createWebHistory(BASE_URL.split('/').slice(3).filter(Boolean).join('/')),
    routes: [
        {path: '/public/*', redirect: '/*'},
        {path: '/', component: Comics, name: 'Last Releases'},
        {path: '/comics', component: Comics, name: 'All Comics'},
        {path: '/recommended', component: Comics, name: 'Recommended'},
        {path: '/comics/:slug', component: Comic},
        {path: '/targets/:target', component: Comics},
        {path: '/genres/:genre', component: Comics},
        {path: '/read/:slug/:lang/:pathMatch(vol/\\d+|ch/\\d+|sub/\\d+)*', component: Read},
        {path: '/:pathMatch(.*)*', component: NotFound},
    ]
})
