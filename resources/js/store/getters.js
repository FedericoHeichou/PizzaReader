let getters = {
    comics: state => {
        return state.comics
    },
    comic: state => {
        return state.comic
    },
    chapter: state => {
        return state.chapter
    },
    page: state => {
        return state.page
    },
    valueLeft: state => {
        return state.valueLeft
    },
    valueRight: state => {
        return state.valueRight
    },
}

export default  getters
