import {RouteRecordRaw} from "vue-router";
import {useStore} from "@/store";

const Auth: Array<RouteRecordRaw> = [
    {
        path: '/login',
        name: 'auth-login',
        component: () =>
            import('../views/pages/auth/Login.vue'),
    },
    {
        path: '/logout',
        name: 'auth-logout',
        component: {
            beforeRouteEnter(to, from, next) {
                const store = useStore();

                console.log({from});
                const destination = {
                    path: from.path || "/",
                    query: from.query,
                    params: from.params
                };
                if (!from) {
                    console.log("no from");
                }
                console.log("running before hook");
                store.dispatch("auth/logout");
                next(destination);
            }
        }
    },


];

export default Auth;