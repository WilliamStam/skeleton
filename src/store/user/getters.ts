import {UserStateInterface} from "./state";

export default {
    token: (state: UserStateInterface) => {
        return state.token
    }
}