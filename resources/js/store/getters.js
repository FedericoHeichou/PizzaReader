import router from '../router/index'

let getters = {
    info: state => {
        return state.info || null;
    },
    socials: state => {
        return state.info ? state.info.socials || [] : [];
    },
    comics: state => {
        switch (router.app.$route.path) {
            case '/':
                return _.sortBy(state.comics.filter(comic => comic.last_chapter !== null), 'last_chapter.published_on').reverse().slice(0, 10);
            case '/recommended':
                return _.sortBy(state.comics.filter(comic => comic.recommended !== 0), 'recommended');
            case '/comics':
                return _.sortBy(state.comics, 'title');
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
