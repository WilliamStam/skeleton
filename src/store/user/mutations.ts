import {UserStateInterface} from "./state";

export default {
    TOKEN(state: UserStateInterface, token: string | false = false): void {
        if (token) {
            sessionStorage.setItem('user-token', token)
            state.token = token
        } else {
            sessionStorage.removeItem('user-token')
            state.token = ""
        }

    },
    USER(state: UserStateInterface, user: UserStateInterface['user'] = false): void {
        state.user = user
    },
    PERMISSIONS(state: UserStateInterface, permissions: UserStateInterface['permissions'] = []): void {
        state.permissions = permissions
    },
}