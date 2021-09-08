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

<script lang="ts">



import PageProfiler from "@/components/PageProfiler.vue"; // @ is an alias to /src
import { useStore } from '@/store';

export default {
    components: {PageProfiler},
    setup() {
        // const axios: any = inject('axios');  // inject axios
        //
        // const api_base : string = "http://skeleton.localhost/api";

        // interface getDataCallable {
        //   (name: object): object;
        // }

        // const getData = (url: string, params: object, callback: getDataCallable): void => {
        //     axios
        //         .get(`${api_base}${url}`)
        //         .then((response: { data: any }) => {
        //             console.log(response.data)
        //
        //         });
        // };

        // return {getData}


        // console.log(profiler);

    },
    created () {
        const store = useStore();


        let profiler_item = JSON.parse(window.profiler)
        if (profiler_item.url){
            store.dispatch("profiler/add",profiler_item);
        }
        // console.log(window.profiler)
        // this.$store.profiler.dispatch('addProfiler',{
        //     "url":"/woof",
        //     "method":"aahhhh",
        //     "items":[]
        // });
    }



}
//
//
// defineComponent({
//   name: "Profiler",
//   components: {
//     Profiler,
//   },
// });
</script>
