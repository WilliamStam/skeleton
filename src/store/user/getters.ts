import {UserStateInterface} from "./state";

export default {
    token: (state: UserStateInterface) => {
        return state.token
    },
    hasPermissions: (state: UserStateInterface, getters: any) => (permission: string | string[]) => {
        if (typeof permission === 'string' || permission instanceof String) {
            return getters.hasPermission(permission)
        }
        return permission.every((perm: string) => getters.hasPermission(perm));
    },
    hasSomePermissions: (state: UserStateInterface, getters: any) => (permission: string | string[]) => {
        if (typeof permission === 'string' || permission instanceof String) {
            return getters.hasPermission(permission)
        }
        return permission.some((perm: string) => getters.hasPermission(perm));
    },
    hasPermission: (state: UserStateInterface) => (permission: string) => {
        if (!state.permissions){
            return false;
        }
        console.log("checking permission",permission)
        return state.permissions.includes(permission)
    },
    routeCheckPermission: (state: UserStateInterface, getters: any) => (permission: string) => {
     if (!state.permissions){
         console.log("permissions are empty. got to go fetch em")
     }
     return getters.hasPermissions(permission);

    },
    fetching: (state: UserStateInterface, getters: any)  => {

     return state.fetching;

    },
    fetched: (state: UserStateInterface, getters: any)  => {
         return state.fetched;
    }

}