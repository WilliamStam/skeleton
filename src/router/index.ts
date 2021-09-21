import {createRouter, createWebHistory, RouteRecordRaw} from "vue-router";
import Home from "@/views/pages/Home.vue";
import Errors from "@/router/errors";

import {Routes as AdminRoutes} from "@/modules/admin";
import {Routes as AuthRoutes} from "@/modules/auth";

const routes: Array<RouteRecordRaw> = [
    {
        path: "/",
        name: "home",
        component: Home,
    },
    {
        path: "/about/:id",
        name: "about",
        props: true,
        component: () =>
            import(/* webpackChunkName: "front" */ "../views/pages/About.vue"),
    },

];


routes.push(...AdminRoutes);
routes.push(...AuthRoutes);


routes.push(...Errors);

const router = createRouter({
    history: createWebHistory(process.env.BASE_URL),
    routes,
});

export default router;
