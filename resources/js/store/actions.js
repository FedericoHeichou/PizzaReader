import router from '../router/index'

let actions = {
    fetchComics({commit}, path) {
        if(router.app.$store.getters.comics.length > 0) return;
        return axios.get(API_BASE_URL + path)
            .then(res => {
                commit('FETCH_COMICS', res.data['comics']);
            }).catch(err => {
            console.log(err);
        });
    },
    fetchComic({commit}, route) {
        if(router.app.$store.getters.comic !== null) return;
        return axios.get(API_BASE_URL + route.path)
            .then(res => {
                if (res.data['comic'] != null && res.data['comic']['chapters'] != null) {
                    res.data['comic']['chapters'].forEach(function (value, index) {
                        this[index]['time'] = timePassed(this[index]['published_on']);
                    }, res.data['comic']['chapters']);
                }
                commit('FETCH_COMIC', res.data['comic']);
            }).catch(err => {
            console.log(err);
        });
    },
    fetchChapter({commit}, path) {
        if(router.app.$store.getters.getChapter(path) !== null) return;
        return axios.get(API_BASE_URL + path)
            .then(res => {
                if(router.app.$store.getters.comic === null) commit('FETCH_COMIC', res.data['comic']);
                commit('FETCH_CHAPTER', res.data['chapter']);
            }).catch(err => {
            console.log(err);
        });
    }
}

export default actions
