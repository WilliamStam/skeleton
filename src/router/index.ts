import {createRouter, createWebHistory, RouteRecordRaw} from "vue-router";
import Home from "@/views/pages/Home.vue";
import Errors from "@/router/errors";

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

    // add in route "modules" like so
    ...Errors,


];


const router = createRouter({
    history: createWebHistory(process.env.BASE_URL),
    routes,
});

export default router;
