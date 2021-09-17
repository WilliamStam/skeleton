import login from './login/state';
import {Commit} from "vuex";

export interface AuthStateInterface {
    token: string
}

export default {
    namespaced: true,
    modules: {
        login,
    },
    state: (): AuthStateInterface => ({
        token: localStorage.getItem('user-token') || '',
    }),
     mutations: {
         setToken(state: AuthStateInterface, token: string): void {
             localStorage.setItem('user-token', token)
             state.token = token
         },
         removeToken(state: AuthStateInterface): void {
             localStorage.removeItem('user-token')
             state.token = ""
         },
     },
     actions: {
        setToken({commit}: { commit: Commit }, token: string): void {
            commit("setToken", token);
        },
        removeToken({commit}: { commit: Commit }): void {
            commit("removeToken");
        },

    },
    getters: {
        token: (state:AuthStateInterface) => {
            return state.token
        }
    }

};
