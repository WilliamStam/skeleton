import actions from './actions'
import mutations from './mutations'
import getters from './getters'
import {UserStateInterface} from "@/store/user";


export interface ResponseMessagesInterface {
    "type": string,
    "message": string,
}

export interface AuthLoginStateInterface {
    messages: ResponseMessagesInterface[],
    active: boolean,
    username: string
}


export default {
    namespaced: true,
    state: (): AuthLoginStateInterface => ({
        messages: [],
        username: localStorage.getItem('username') || '',
        active: false
    }),
    actions,
    mutations,
    getters
}