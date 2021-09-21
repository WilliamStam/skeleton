import {Commit, Dispatch} from "vuex";

import {UserStateInterface} from "./state"
import api from "@/composables/api";

export default {
    token({commit}: { commit: Commit }, token: string | false = false): void {
        commit("TOKEN", token);
    },
    async fetch({commit}: { commit: Commit }): Promise<void> {

        const response = await api.get("/api/auth/user", {}, {
            loading: true
        }) as {
            user?: UserStateInterface['user']
            permissions?: UserStateInterface['permissions']
        };

        commit('USER', response.user || false)
        commit('PERMISSIONS', response.permissions || [])


    },

}