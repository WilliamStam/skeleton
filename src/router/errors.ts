import {RouteRecordRaw} from "vue-router";

const Errors: Array<RouteRecordRaw> = [
    {
        path: '/404',
        name: '404',
        component: () =>
            import('../views/errors/404.vue'),
    },
    {
        path: '/500',
        name: '500',
        component: () =>
            import('../views/errors/500.vue'),
    },

    // if route doesnt exist then route here

    {
        path: '/:pathMatch(.*)*',
        component: () =>
            import('../views/errors/404.vue'),

    }


];

export default Errors;