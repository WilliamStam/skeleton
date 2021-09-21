import {RouteRecordRaw} from "vue-router";
import AuthStore from "./store/state";
import {createStore} from "vuex";

import {useStore} from "@/store";

export const Routes: Array<RouteRecordRaw> = [
    {
        path: '/login',
        name: 'auth.login',
        component: () =>
            import('./views/pages/Login.vue'),
    },
    {
        path: '/logout',
        name: 'auth.logout',
        component: {
            beforeRouteEnter(to, from, next) {
                const store = useStore();

                const destination = {
                    path: from.path || "/",
                    query: from.query,
                    params: from.params
                };
                if (!from) {
                    console.log("no from");
                }
                console.log("running before hook!");
                store.dispatch("auth/logout",{},{root: true});
                next(destination);
            }
        }
    },
];

export const Store = AuthStore;





