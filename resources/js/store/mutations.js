import router from '../router/index'

let mutations = {
    FETCH_COMICS(state, comics) {
        return state.comics = comics;
    },
    FETCH_COMIC(state, comic) {
        return state.comic.push(comic);
    },
    FETCH_CHAPTER(state, chapter) {
        let comic = router.app.$store.getters.comic
        if(chapter === null && comic !== null) {
            router.push(comic.url);
        }
        return state.chapter.push(chapter);
    },
};

export default mutations
