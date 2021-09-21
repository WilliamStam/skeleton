import actions from './actions'
import mutations from './mutations'
import getters from './getters'

//
// export interface ResponseMessagesInterface {
//     "type": string,
//     "message": string,
// }
//
export interface StateInterface {
    list: []
}


export default {
    namespaced: true,
    state: (): StateInterface => ({
        list: []
    }),
    actions,
    mutations,
    getters
}