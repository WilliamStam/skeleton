import {Commit, Dispatch} from "vuex";
import api from "@/composables/api";
import {ResponseMessagesInterface} from "./state";

import {UserStateInterface} from "@/store/user/state"

export default {
    async login({
                     dispatch,
                     commit
                 }: { dispatch: Dispatch, commit: Commit }, form: { username: string, password: string }): Promise<void> {

        commit("ACTIVE", false);
        const response = await api.post("/api/auth/login", {...form}, {
            loading: true
        }) as {
            messages: ResponseMessagesInterface[],
            active: boolean,
            token?: string
            user?: UserStateInterface['user']
            permissions?: UserStateInterface['permissions']
        };

        commit("ACTIVE", response.active ? true : false);
        commit("MESSAGES", response.messages);

        dispatch('user/token', response.token ? response.token : false, {root: true})
        dispatch('user/user', response.user || false, {root: true})
        dispatch('user/permissions', response.permissions || [], {root: true})





    },
    async check({dispatch, commit}: { dispatch: Dispatch, commit: Commit }): Promise<void> {

        // commit("USERNAME", "william@munsoft");

        const response = await api.get("/api/auth/login", {}, {
            loading: true
        }) as {
            messages: ResponseMessagesInterface[],
            active: boolean,
            user?: UserStateInterface['user']
            permissions?: UserStateInterface['permissions']
        };


        commit("ACTIVE", response.active ? true : false);
        commit("MESSAGES", response.messages);

        dispatch('user/user', response.user || false, {root: true})
        dispatch('user/permissions', response.permissions || [], {root: true})

    },
    async logout({dispatch, commit}: { dispatch: Dispatch, commit: Commit }): Promise<void> {

        // commit("USERNAME", "william@munsoft");

        await api.get("/api/auth/logout", {}, {
            loading: true
        });

        dispatch('user/token',false,{root: true});
        dispatch('user/user',false,{root: true});
        dispatch('user/permissions',[],{root: true});

        dispatch('check');




    },
}