<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <img alt="test image" src="/media/wallhaven-207882.jpg?h=200"/>
                <HelloWorld msg="Welcome to Your Vue.js + TypeScript App"/>
            </div>
        </div>

    </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";
// import api from '@/composables/api';
import HelloWorld from "@/components/HelloWorld.vue"; // @ is an alias to /src
// import GetData from "@/mixins/GetData";

export default defineComponent({
    name: "Home",
    components: {
        HelloWorld,
    },

    mounted() {
        console.log("mounted");
        // this.api("@test_home?id=666",{},function(response: any){
        //     console.log("callback @",response);
        // });
        // this.api("/test/123",{},function(response: any){
        //     console.log("callback /",response);
        // });




        // GetData("a",{},function(response){
        //     console.log(response)
        // })
    }
});
</script>
