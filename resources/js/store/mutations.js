export const mutations = {
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
        if (!comic) return;
        if (comic['chapters'] != null) {
            comic['chapters'] = comic['chapters'].map(c => { return {...c, time: timePassed(c['published_on'])} });
        }
        return state.comics_obj[comic.slug] = comic;
    },
    FETCH_CHAPTER(state, chapter) {
        if (!chapter) return;
        return state.chapters_obj[chapter.url] = chapter;
    },
    FETCH_VOTE(state, vote) {
        state.vote_token = vote['vote_token'];
        return state.votes[vote.vote_id] = vote.your_vote;
    },
};
