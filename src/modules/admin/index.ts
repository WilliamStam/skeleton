import {RouteRecordRaw} from "vue-router";
import {useStore} from "@/store";
import {createStore} from "vuex";

import RolesStore from "./store/roles/state";

export const Routes: Array<RouteRecordRaw> = [
    {
        path: "/admin/roles",
        name: "admin.roles",
        component: () => import(/* webpackChunkName: "admin" */ "./views/pages/Roles.vue"),
        // beforeEnter: requireAuth,
        meta: {
            permission: 'test.perm.1'
        }
    },
    {
        path: "/admin/roles/categories",
        name: "admin.roles.categories",
        component: () => import(/* webpackChunkName: "admin" */ "./views/pages/RolesCategories.vue"),
        // beforeEnter: requireAuth,
        meta: {
            permission: 'test.perm.2'
        }
    },
];

export const Store = {
    namespaced:true,
    modules: {
       roles: RolesStore
    },
};


