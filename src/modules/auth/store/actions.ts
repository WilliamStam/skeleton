import {Commit, Dispatch} from "vuex";
import api from "@/composables/api";
import {ResponseMessagesInterface} from "./state";


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
        };

        commit("ACTIVE", response.active ? true : false);
        commit("MESSAGES", response.messages);

        dispatch('user/token', response.token ? response.token : false, {root: true});

        dispatch('user/fetch',{}, {root: true})
    },
    async check({
                     dispatch,
                     commit
                 }: { dispatch: Dispatch, commit: Commit }, form: { username: string, password: string }): Promise<void> {

        commit("ACTIVE", false);
        const response = await api.get("/api/auth/login", {}, {
            loading: true
        }) as {
            messages: ResponseMessagesInterface[],
            active: boolean,
        };

        commit("ACTIVE", response.active ? true : false);
        commit("MESSAGES", response.messages);


    },

    async logout({dispatch, commit}: { dispatch: Dispatch, commit: Commit }): Promise<void> {

        // commit("USERNAME", "william@munsoft");

        await api.get("/api/auth/logout", {}, {
            loading: true
        }).then(() => {
            dispatch('user/token', false, {root: true});

        }).catch((error)=>{
            // oops couldn't log you out
        });

        dispatch('user/fetch', {}, {root: true})




    },
}