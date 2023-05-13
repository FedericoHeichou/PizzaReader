export const actions = {
    fetchInfo({commit}) {
        const store = this;
        if(store.getters.info !== null) return;
        return axios.get(API_BASE_URL + '/info')
            .then(res => {
                commit('FETCH_INFO', res.data['info']);
            }).catch(err => {
                console.log(err);
            });
    },
    fetchComics({commit}, path) {
        const store = this;
        if(store.getters.comics.length > 0) return;
        const loader = $('#loader');
        loader.show();
        return axios.get(API_BASE_URL + path)
            .then(res => {
                commit('FETCH_COMICS', res.data['comics']);
            }).catch(err => {
                console.log(err);
            }).finally(
                () => loader.hide()
            );
    },
    fetchComic({commit}, route) {
        const store = this;
        if(store.getters.comic !== null) return;
        const loader = $('#loader');
        loader.show();
        return axios.get(API_BASE_URL + route.path, { params: { licensed: true } })
            .then(res => {
                commit('FETCH_COMIC', res.data['comic']);
            }).catch(err => {
                console.log(err);
            }).finally(
                () => loader.hide()
            );
    },
    fetchChapter({commit}, path) {
        const store = this;
        if(store.getters.getChapter(path) !== null) return;
        const comic = store.getters.comic;
        const loader = $('#loader');
        loader.show();
        return axios.get(API_BASE_URL + path)
            .then(res => {
                if(comic === null) commit('FETCH_COMIC', res.data['comic']);
                commit('FETCH_CHAPTER', res.data['chapter']);
            }).catch(err => {
                console.log(err);
            }).finally(
                () => loader.hide()
            );
    },
    fetchVote({commit}, vote_id) {
        return axios.get(`${API_BASE_URL}/vote/${vote_id}`)
            .then(res => {
                commit('FETCH_VOTE', res.data);
            }).catch(err => {
                console.log(err);
            });
    }
}
