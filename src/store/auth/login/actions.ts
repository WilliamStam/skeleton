import {Commit, Dispatch} from "vuex";
import api from "@/composables/api";
import {ResponseMessagesInterface} from "./state";

export default {
    async submit({dispatch,commit}: { dispatch: Dispatch,commit: Commit }, form: { username: string, password: string }): Promise<void> {

        commit("ACTIVE", false);
        const response = await api.post("/api/auth/login", {...form}, {
            loading: true
        }) as { messages: ResponseMessagesInterface[], active: boolean, token?: string };

        commit("ACTIVE", response.active ? true : false);
        commit("MESSAGES", response.messages);

        if (response.token){
            dispatch('auth/setToken', response.token, {root:true})
        }
    },
    async load({commit}: { commit: Commit }): Promise<void> {

        // commit("USERNAME", "william@munsoft");

        const response = await api.get("/api/auth/login", {}, {
            loading: true
        }) as { messages: ResponseMessagesInterface[], active: boolean };


        commit("ACTIVE", response.active ? true : false);
        commit("MESSAGES", response.messages);

    },
}