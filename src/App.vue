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

export default {
    components: {PageProfiler},
    setup() {


    },
    created () {
        const store = useStore();


        let profiler_item = JSON.parse(window.profiler)
        if (profiler_item.url){
            store.dispatch("profiler/add",profiler_item);
        }

    }



}

</script>
