import { router } from "../router";

export const getters = {
    info: state => {
        return state.info || null;
    },
    socials: state => {
        return state.info ? state.info.socials || [] : [];
    },
    comics: (state, getters) => {
        const route = router.currentRoute.value;
        switch (route.path) {
            case '/':
                return _.sortBy(state.comics.filter(comic => comic.last_chapter !== null), 'last_chapter.published_on').reverse().slice(0, 10);
            case '/recommended':
                return _.sortBy(state.comics.filter(comic => comic.recommended !== 0), 'recommended');
            case '/comics':
                return _.sortBy(state.comics, 'title');
            default:
                if (route.path.startsWith('/targets/')) {
                    const target = route.params.target.toLowerCase();
                    return _.sortBy(state.comics.filter(comic => comic.target && comic.target.toLowerCase() === target), 'title');
                } else if (route.path.startsWith('/genres/')) {
                    const genre = route.params.genre.toLowerCase();
                    return _.sortBy(state.comics.filter(comic => comic.genres && comic.genres.map(g => g.slug).includes(genre)), 'title');
                } else {
                    return state.comics;
                }

        }
    },
    getComic: state => (slug) => {
        return state.comics_obj[slug] || null;
    },
    comic: (state, getters) => {
        const route = router.currentRoute.value;
        const comic = getters.getComic(route.params.slug) || null;
        return comic;
    },
    getChapter: state => (path) => {
        return state.chapters_obj[path] || null;
    },
    chapter: (state, getters) => {
        const route = router.currentRoute.value;
        const chapter = getters.getChapter(route.path) || null;
        return chapter;
    },
    vote: state => (vote_id) => {
        return state.votes[vote_id] || 0;
    },
    vote_token: state => {
        return state.vote_token || null;
    },
};
