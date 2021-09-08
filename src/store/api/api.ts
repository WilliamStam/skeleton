import {AxiosInstance} from 'axios'
import { Commit } from 'vuex';
export interface ApiCall {
    key: string,
    instance: AxiosInstance,
    options: {

    }
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
            // console.log("mutation addProfiler", item)
            state.list.push(item);

        }
    },
    actions: {
        add({commit}: { commit: Commit}, item: ApiCall): void {

            commit("add", item)

        }
    },
}

