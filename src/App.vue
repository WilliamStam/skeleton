<template>

    <PageMenu></PageMenu>

    <router-view id="page-content"></router-view>

    <PageFooter></PageFooter>

    <PageProfiler v-if="show_profiler"></PageProfiler>
    <LoadingMask></LoadingMask>

</template>

<style lang="scss">
#app {
    padding-top:3rem;
}
</style>

<script >
/* eslint-disable */
import PageProfiler from "@/views/components/PageProfiler.vue"; // @ is an alias to /src
import { useStore } from '@/store';
import LoadingMask from "@/views/components/LoadingMask";
import PageMenu from "@/views/components/PageMenu.vue";
import PageFooter from "@/views/components/PageFooter.vue";

export default {
    components: {
        LoadingMask,
        PageProfiler,
        PageMenu,
        PageFooter,
    },

    created () {
        const store = useStore();
        let profiler_item = JSON.parse(window.profiler)
        if (profiler_item.url){
            store.dispatch("api/addProfiler",profiler_item);
        }
        this.$store.dispatch("user/fetch");
    },
    computed: {
        show_profiler(){
            return this.$store.state.api.profiler.length
        }
    }



}

</script>
