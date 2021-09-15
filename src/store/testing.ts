import {Commit, Dispatch} from 'vuex';
import {ActiveRequestRecord, ProfilerRecord, ProfilerRecordItems} from "@/store/api";

export interface CanvasItem {
    "label": string,
    "id": string,
    "x": number,
    "y": number,
    "width": number,
    "height": number,
}

export interface TestingState {
    list: CanvasItem[],
}


export default {
    namespaced: true,
    state: (): TestingState => ({
        list: [],
    }),
    mutations: {
        addItem(state: TestingState, item: CanvasItem): void {
            state.list.push(item);
        },

    },
    actions: {

        addItem({commit, state}: { commit: Commit, state: TestingState }, item:CanvasItem):void {

            console.log(item)
            commit("addItem", item);
        },

    },


}

