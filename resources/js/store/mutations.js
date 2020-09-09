import router from '../router/index'

let mutations = {
    FETCH_COMICS(state, comics) {
        return state.comics = comics
    },
    FETCH_COMIC(state, comic) {
        return state.comic = comic
    },
    FETCH_CHAPTER(state, chapter) {
        state.chapter = chapter
        if(chapter == null) {
            router.push(state.comic.url)
        }
    },
    setPage(state, page) {
        return state.page = parseInt(page)
    },
}
export default mutations
