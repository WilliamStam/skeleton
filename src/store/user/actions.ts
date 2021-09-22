import {Commit, Dispatch} from "vuex";

import {UserStateInterface} from "./state"
import api from "@/utilities/api";

export default {
    token({commit}: { commit: Commit }, token: string | false = false): void {
        commit("TOKEN", token);
    },
    async fetch({commit}: { commit: Commit }): Promise<any> {

        commit('FETCHING',true)
        const response = await api.get("/api/user", {}, {
            loading: true
        }) as {
            user?: UserStateInterface['user']
            permissions?: UserStateInterface['permissions']
        };

        commit('USER', response.user || false)
        commit('PERMISSIONS', response.permissions || [])
        commit('FETCHING',false)
        commit('FETCHED')

    },

}