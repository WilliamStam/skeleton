import {Commit} from 'vuex';

export interface ProfilerItemItems {
    "label": string,
    "component": string,
    "data": null | Array<unknown>,
    "time": {
        "start": number,
        "end": number,
        "total": number,
        "offset": number,
        "percent": number
    }
}

export interface ProfilerItem {
    "url": string,
    "method": string,
    "items": Array<ProfilerItemItems>[]
    "total": {
        "time": number,
        "memory": number
    }
}

export interface ProfilerState {
    list: Array<ProfilerItem>
}


export default {
    namespaced: true,
    state: (): ProfilerState => ({
        list: []
    }),
    mutations: {
        add(state: ProfilerState, item: ProfilerItem): void {
            // add item to the beginning of the list
            state.list.splice(0, 0, item);
        }
    },
    actions: {
        add({commit}: { commit: Commit }, item: ProfilerItem): void {
            commit("add", item)
        }
    },
}