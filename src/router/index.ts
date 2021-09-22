import {createRouter, createWebHistory, RouteRecordRaw} from "vue-router";
import Home from "@/views/pages/Home.vue";
import Errors from "@/router/errors";

import {Routes as AdminRoutes} from "@/modules/admin";
import {Routes as AuthRoutes} from "@/modules/auth";

import {useStore} from '@/store';

const store = useStore()

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

router.beforeEach(async (to) => {
    // instead of having to check every route record with
    // to.matched.some(record => record.meta.requiresAuth)



    if (to.meta.permission){
        // if (!Store.state.user.fetched){

        if (!store.getters["user/fetched"]){
            console.log("we need to fetch the user now");
            await store.dispatch("user/fetch");
            console.log("after fetching the user");
        }
        if (to.meta.permission && !store.getters["user/routeCheckPermission"](to.meta.permission)) {
            return {
                path: '/login',
                // save the location we were at to come back later
                query: {redirect: to.fullPath},
            }
        }


        // }
        console.log("to.meta.permission",to.meta.permission,store.getters["user/routeCheckPermission"](to.meta.permission));
    }


    // if (to.meta.permission && Store.getters["user/routeCheckPermission"](to.meta.permission)) {
    //
    //     // this route requires auth, check if logged in
    //     // if not, redirect to login page.
    //     return {
    //         path: '/login',
    //         // save the location we were at to come back later
    //         query: {redirect: to.fullPath},
    //     }
    //
    //
    // }
})

export default router;
