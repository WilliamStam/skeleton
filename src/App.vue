<template>

    <PageMenu></PageMenu>


    <router-view id="page-content"></router-view>
    <PageFooter></PageFooter>

    <PageProfiler></PageProfiler>
    <LoadingMask></LoadingMask>

</template>

<style lang="scss">
#page-content {
    padding-top:3rem;
}
</style>

<script >
/* eslint-disable */
import PageProfiler from "@/components/PageProfiler.vue"; // @ is an alias to /src
import { useStore } from '@/store';
import LoadingMask from "@/components/LoadingMask";
import PageMenu from "@/views/components/PageMenu.vue";
import PageFooter from "@/views/components/PageFooter.vue";

export default {
    components: {
        LoadingMask,
        PageProfiler,
        PageMenu,
        PageFooter,
    },
    setup() {



    },
    created () {
        const store = useStore();


        let profiler_item = JSON.parse(window.profiler)
        if (profiler_item.url){
            store.dispatch("api/addProfiler",profiler_item);
        }

        store.dispatch("system/getSystemInfo");

    }



}

</script>
