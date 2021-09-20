import {Commit} from "vuex";
import actions from './actions'
import mutations from './mutations'
import getters from './getters'


export interface UserStateInterface {
    token: string,
    user: {
        id: string,
        name: string,
        email: string
    } | false,
    permissions: string[]
}

export default {
    namespaced: true,
    modules: {},
    state: (): UserStateInterface => ({
        token: sessionStorage.getItem('user-token') || '',
        user: false,
        permissions: []
    }),
    actions,
    mutations,
    getters

};
