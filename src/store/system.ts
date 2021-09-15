import {Commit, Dispatch} from 'vuex';
import api from "@/composables/api";

export interface SystemState {
    debug: boolean,
}


export default {
    namespaced: true,
    state: (): SystemState => ({
        debug: false,
    }),
    mutations: {
        updateSystem(state: SystemState, item: SystemState): void {
            state = item;
        },

    },
    actions: {

        async getSystemInfo({commit, state}: { commit: Commit, state: SystemState }): Promise<unknown> {
            const item = await api.get("/api",{},{
                loading:false
            });

            commit("updateSystem",item);

            return item;

            // commit("updateSystem", );
        },

    },


}

