import { createStore } from 'vuex';
import { actions } from './actions';
import { mutations } from './mutations';
import { getters } from './getters';
import { state } from './state';

export const store = createStore({
    state,
    mutations,
    getters,
    actions
});
