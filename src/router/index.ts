import {createRouter, createWebHistory, RouteRecordRaw} from "vue-router";
import Home from "@/views/pages/Home.vue";
import Errors from "@/router/errors";
import Auth from "@/router/auth";

const routes: Array<RouteRecordRaw> = [
    {
        path: "/",
        name: "Home",
        component: Home,
    },
    {
        path: "/about/:id",
        name: "About",
        props: true,
        component: () =>
            import(/* webpackChunkName: "about" */ "../views/pages/About.vue"),
    },
    {
        path: "/contact",
        name: "Contact",
        component: () =>
            import(/* webpackChunkName: "about" */ "../views/pages/Contact.vue"),
    },
    {
        path: "/test",
        name: "Test",
        component: () =>
            import(/* webpackChunkName: "about" */ "../views/pages/Testing.vue"),
    },

    // add in route "modules" like so
    ...Auth,
    ...Errors,



];


const router = createRouter({
    history: createWebHistory(process.env.BASE_URL),
    routes,
});

export default router;
