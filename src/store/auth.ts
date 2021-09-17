import {Commit, Dispatch} from 'vuex';



export interface ResponseMessagesInterface {
    "type": string,
    "message": string,

}

export interface AuthRequestInterface {
    "username": string,
    "attempts": number,
    "messages"?: ResponseMessagesInterface[],

}

export interface AuthStateInterface {
    auth: UserAuthInterface,
}


export default {
    namespaced: true,
    state: (): UserStateInterface => ({
        auth: {
            username: "",
            attempts: 0,
            messages: [],
        },
    }),
    mutations: {
        AUTH(state: UserStateInterface, item: UserAuthInterface): void {
            state.auth = item;
        },

    },
    actions: {
        login({commit}: { commit: Commit }, item:CanvasItem):void {

            console.log(item)
            commit("addItem", item);
        },

    },
}

