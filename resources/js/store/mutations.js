let mutations = {
    FETCH_COMICS(state, comics) {
        return state.comics = comics
    },
    FETCH_COMIC(state, comic) {
        return state.comic = comic
    },
    FETCH_CHAPTER(state, chapter) {
        return state.chapter = chapter
    },
    setPage(state, page) {
        return state.page = parseInt(page)
    },
    invertDirection(state) {
        state.valueRight *= -1;
        state.valueLeft *= -1;
    }
}
export default mutations
