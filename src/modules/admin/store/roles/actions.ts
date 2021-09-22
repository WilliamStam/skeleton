import {Commit, Dispatch} from "vuex";
import api from "@/utilities/api";
// import {ResponseMessagesInterface} from "./state";


export default {
    async fetchList({
                     dispatch,
                     commit
                 }: { dispatch: Dispatch, commit: Commit }): Promise<void> {

       console.log("Fetching the list")
    },
    async fetchItem({
                     dispatch,
                     commit
                 }: { dispatch: Dispatch, commit: Commit }): Promise<void> {

       console.log("Fetching an item")
    },

}