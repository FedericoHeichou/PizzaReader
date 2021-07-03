import router from '../router/index'

let getters = {
    comics: state => {
        switch (router.app.$route.path) {
            case '/alph':
                return _.sortBy(state.comics, 'title');
            case '/last':
                return _.sortBy(state.comics, 'last_chapter.published_on').reverse();
            default:
                return state.comics;

        }
    },
    getComic: state => (slug) => {
        return state.comic.find(comic => comic.slug === slug) || null;
    },
    comic: (state, getters) => {
        return getters.getComic(router.app.$route.path.split('/', 3)[2]) || null;
    },
    getChapter: state => (path) => {
        return state.chapter.find(chapter => chapter.url === path) || null;
    },
    chapter: (state, getters) => {
        return getters.getChapter(router.app.$route.path) || null;
    },
};

export default getters;
