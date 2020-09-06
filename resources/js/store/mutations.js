let mutations = {
    FETCH_COMICS(state, comics) {
        return state.comics = comics
    },
    FETCH_COMIC(state, comic) {
        return state.comic = comic
    }
}
export default mutations
