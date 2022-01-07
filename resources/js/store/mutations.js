import router from '../router/index'

let mutations = {
    FETCH_INFO(state, info) {
        return state.info = info;
    },
    FETCH_COMICS(state, comics) {
        if (comics != null) {
            return state.comics = comics.map(c => c['last_chapter'] != null ? {...c, last_chapter: {...c['last_chapter'], time: timePassed(c['last_chapter']['published_on'], true)}} : c);
        }
        return state.comics = comics;
    },
    FETCH_COMIC(state, comic) {
        if (comic != null && comic['chapters'] != null) {
            comic['chapters'] = comic['chapters'].map(c => { return {...c, time: timePassed(c['published_on'])} });
        }
        return state.comic.push(comic);
    },
    FETCH_CHAPTER(state, chapter) {
        const comic = router.app.$store.getters.comic;
        if (chapter === null && comic !== null) {
            router.push(comic.url);
        }
        return state.chapter.push(chapter);
    },
    FETCH_VOTE(state, vote) {
        state.vote_token = vote['vote_token'];
        return state.votes = {...state.votes, [vote.vote_id]: vote.your_vote};
    }
};

export default mutations
