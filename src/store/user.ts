import {Commit, Dispatch} from 'vuex';
import api from "@/composables/api";
import {SystemState} from "@/store/system";


export interface ResponseMessagesInterface {
    "type": string,
    "message": string,

}

export interface AuthLoginInterface {
    "username": string,
    "attempts": number,
    "messages"?: ResponseMessagesInterface[],

}
export interface AuthLoginFormInterface {
    "username": string,
    "password": string,

}
export interface AuthUserInterface {
    "id": string,
    "name": string,
    "email": string,

}

export interface UserStateInterface {
    login: AuthLoginInterface,
    user: AuthUserInterface,
}


export default {
    namespaced: true,
    state: (): UserStateInterface => ({
        login: {
            username: "",
            attempts: 0,
            messages: [],
        },
        user: {
            id: "",
            name: "",
            email: "",
        },

    }),
    mutations: {
        LOGIN(state: UserStateInterface, item: AuthLoginInterface): void {
            state.login = item;
        },
        USER(state: UserStateInterface, user: AuthUserInterface): void {
            state.user = user;
        },

    },
    actions: {
        async login({commit}: { commit: Commit }, form:AuthLoginFormInterface): Promise<unknown> {
            const item = await api.post("/api/auth/login",{...form},{
                loading:true
            });

            // commit("LOGIN",item);
            // commit("USER",{
            //     id: item.user.id,
            //     name: item.user.name,
            //     email: item.user.email,
            // });

            console.log(item);
            return item;

            // commit("updateSystem", );
        },


        // login({commit}: { commit: Commit }, item:AuthLoginFormInterface):void {
        //
        //     console.log(item)
        //
        //
        //
        //     commit("AUTH", {
        //          "username": item.username,
        //         "attempts": number,
        //         "messages"?: ResponseMessagesInterface[],
        //     });
        // },

    },
}

