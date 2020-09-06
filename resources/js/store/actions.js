import router from '../router/index.js'

let actions = {
    fetchComics({commit}) {
        axios.get(API_BASE_URL + '/comics')
            .then(res => {
                commit('FETCH_COMICS', res.data['comics'])
            }).catch(err => {
            console.log(err)
        })
    },
    fetchComic({commit}) {
        axios.get(API_BASE_URL + '/comics/' + router.currentRoute.params.slug)
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
    }
}

export default actions
