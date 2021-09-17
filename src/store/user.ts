import {Commit, Dispatch} from 'vuex';
import api from "@/composables/api";
import {SystemState} from "@/store/system";



export interface AuthUserInterface {
    "id": string,
    "name": string,
    "email": string,
}

export interface UserStateInterface {
    user: AuthUserInterface,
}


export default {
    namespaced: true,
    state: (): UserStateInterface => ({
        user: {
            id: "",
            name: "",
            email: "",
        },
    }),
    mutations: {
        USER(state: UserStateInterface, user: AuthUserInterface): void {
            state.user = user;
        },

    },
    actions: {


    },
}

