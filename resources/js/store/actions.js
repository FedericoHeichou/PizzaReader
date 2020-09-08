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
        axios.get(API_BASE_URL + route.path)
            .then(res => {
                if (res.data['comic']['chapters'] != null) {
                    res.data['comic']['chapters'].forEach(function (value, index) {
                        this[index]['time'] = timePassed(this[index]['published_on']);
                    }, res.data['comic']['chapters']);
                }
                commit('FETCH_COMIC', res.data['comic'])
            }).catch(err => {
            console.log(err)
        })
    },
    fetchChapter({commit}, route) {
        axios.get(API_BASE_URL + route.path)
            .then(res => {
                commit('FETCH_COMIC', res.data['comic']);
                commit('FETCH_CHAPTER', res.data['chapter']);
                let page = 1;
                let hash = route.hash.substring(1);
                if(hash === "last" || parseInt(hash) > res.data['chapter']['pages'].length) {
                    page = res.data['chapter']['pages'].length;
                }else {
                    page = isNaN(parseInt(hash)) || parseInt(hash) < 1 ? 1 : parseInt(hash);
                }
                commit('setPage', page);
                if(history.pushState) {
                    history.pushState(null, null, '#' + page);
                } else {
                    location.hash = '#' + page;
                }
            }).catch(err => {
            console.log(err)
        })
    }
}

export default actions
