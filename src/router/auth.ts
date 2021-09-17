import {RouteRecordRaw} from "vue-router";

const Auth: Array<RouteRecordRaw> = [
    {
        path: '/auth/login',
        name: 'auth-login',
        component: () =>
            import('../views/pages/auth/Login.vue'),
    },
    {
        path: '/auth/logout',
        name: 'auth-logout',
        component: () =>
            import('../views/pages/auth/Login.vue'),
    },


];

export default Auth;