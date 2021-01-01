let actions = {
    fetchComics({commit}) {
        axios.get(API_BASE_URL + '/comics')
            .then(res => {
                commit('FETCH_COMICS', res.data['comics'])
            }).catch(err => {
            console.log(err)
        })
    },
    fetchComic({commit}, route) {
        return axios.get(API_BASE_URL + route.path)
            .then(res => {
                if (res.data['comic'] != null && res.data['comic']['chapters'] != null) {
                    res.data['comic']['chapters'].forEach(function (value, index) {
                        this[index]['time'] = timePassed(this[index]['published_on']);
                    }, res.data['comic']['chapters']);
                }
                commit('FETCH_COMIC', res.data['comic'])
            }).catch(err => {
            console.log(err)
        })
    },
    fetchChapter({commit}, path) {
        return axios.get(API_BASE_URL + path)
            .then(res => {
                commit('FETCH_COMIC', res.data['comic']);
                commit('FETCH_CHAPTER', res.data['chapter']);
            }).catch(err => {
            console.log(err)
        })
    },
    fetchComicsFiltered({commit}, path) {
        axios.get(API_BASE_URL + path)
            .then(res => {
                commit('FETCH_COMICS', res.data['comics'])
            }).catch(err => {
            console.log(err)
        })
    }
}

export default actions
