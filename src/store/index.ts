import {createStore, Store} from 'vuex';
import api from './api';
import system from './system';
import testing from './testing';
import auth from './auth/index';
import user from './user';
import {State} from "@vue/runtime-core";

export const store = createStore({
    // adding modules in
    modules: {
        api,
        system,
        testing,

        user,

         auth,
    },
});

export default store;

export function useStore(): Store<unknown> {
    return store;
}
