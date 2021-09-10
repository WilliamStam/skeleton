import {AxiosInstance} from 'axios'
import { Commit } from 'vuex';


type EmptyKeyValueObject = {
    [key: string]: number | string,
}

export interface ApiCall {
    key: string,
    instance: AxiosInstance,
    config: EmptyKeyValueObject
}
export interface ApiState {
    list: Array<ApiCall>
    // list: [{key: string,instance: AxiosInstance}]
}
export default {
    namespaced: true,
    state: (): ApiState =>({
        list: []
    }),
    mutations: {
        add(state: ApiState, item: ApiCall): void {
            state.list.push(item);
        }
    },
    actions: {
        add({commit}: { commit: Commit}, item: ApiCall): void {
            commit("add", item)
        }
    },
    getters: {
        getList : (state:ApiState) : Array<ApiCall> =>{
            return state.list;
        },
    }
}

