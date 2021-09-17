import {Commit, Dispatch} from 'vuex';

export interface ProfilerRecordItems {
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

export interface ProfilerRecord {
    "url": string,
    "method": string,
    "items": Array<ProfilerRecordItems>[]
    "total": {
        "time": number,
        "memory": number
    }
}


export interface ActiveRequestRecord {
    key: string,
    config: {[key:string]: string | boolean | null}
}


export interface ApiState {
    active: Array<ActiveRequestRecord>,
    profiler: Array<ProfilerRecord>
    // list: [{key: string,instance: AxiosInstance}]
}


export default {
    namespaced: true,
    state: (): ApiState => ({
        active: [],
        profiler: []
    }),
    mutations: {

        addProfiler(state: ApiState, item: ProfilerRecord): void {
            state.profiler.splice(0, 0, item);
        },

        addActive(state: ApiState, item: ActiveRequestRecord): void {
            state.active.push(item);
        },
        removeActive(state: ApiState, key: string): void {
            const new_list = state.active.filter((item) => item.key != key);
            state.active = new_list;
        }
    },
    actions: {


        addActive({commit, state}: { commit: Commit, state: ApiState }, item: ActiveRequestRecord): void {
            commit("addActive", item);
        },
        removeActive({commit, state}: { commit: Commit, state: ApiState }, key: string): void {
            commit("removeActive", key)
        },

        addProfiler({commit}: { commit: Commit }, item: ProfilerRecord): void {
            commit("addProfiler", item)
        }
    },
    getters: {
        showLoading: (state:ApiState) => {
            return state.active.filter(item => item?.config?.loading).length
        },

    }

}

