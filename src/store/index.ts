import {createStore, Store} from 'vuex';
import api from './api';
import {Store as AuthStore} from '@/modules/auth';
import {Store as AdminStore} from '@/modules/admin';
import user from './user/state';

const store = createStore({
    // adding modules in
    modules: {
        api,
        user,
    },
});

store.registerModule('auth',AuthStore);
store.registerModule('admin',AdminStore);


export default store;

export function useStore(): Store<unknown> {
    return store;
}
