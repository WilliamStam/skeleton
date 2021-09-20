import {Commit, Dispatch} from "vuex";

import {UserStateInterface} from "./state"

export default {
    token({commit}: { commit: Commit }, token: string | false = false): void {
        commit("TOKEN", token);
    },
    user({commit}: { commit: Commit }, user: UserStateInterface['user'] | false): void {
        commit("USER", user);
    },
    permissions({commit}: { commit: Commit }, permissions: UserStateInterface['permissions']): void {
        commit("PERMISSIONS", permissions);
    },
}