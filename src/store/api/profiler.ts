export default {
    namespaced: true,
    state: () => ({
        list: []
    }),
    mutations: {
        add(state: any, item: any): void {
            // add item to the beginning of the list
            state.list.splice(0,0,item);
        }
    },
    actions: {
        add({commit, state}: { commit: any, state: any }, item: any): void {
            commit("add", item)
        }
    },
}