<template>
    <div id="nav">
        <router-link to="/">Home</router-link>
        |
        <router-link :to="{path:`/about/hhh`}">About</router-link>
        |
        <router-link :to="{path: '/contact'}">Contact</router-link>
    </div>
    <div id="page">

        <router-view/>
    </div>

    <PageProfiler></PageProfiler>
    <LoadingMask></LoadingMask>

</template>

<style lang="scss">

#nav {
    padding: 30px;

    a {
        font-weight: bold;
        color: #2c3e50;

        &.router-link-exact-active {
            color: #42b983;
        }
    }
}
</style>

<script >
/* eslint-disable */
import PageProfiler from "@/components/PageProfiler.vue"; // @ is an alias to /src
import { useStore } from '@/store';
import LoadingMask from "@/components/LoadingMask";

export default {
    components: {
        LoadingMask,
        PageProfiler},
    setup() {


    },
    created () {
        const store = useStore();


        let profiler_item = JSON.parse(window.profiler)
        if (profiler_item.url){
            store.dispatch("api/addProfiler",profiler_item);
        }

    }



}

</script>
