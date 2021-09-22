import {createApp} from "vue";
import App from "./App.vue";
import router from "@/router";
import store from "@/store";
import axios from 'axios'
import VueAxios from 'vue-axios'
import '@/assets/css/styles.scss'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
import {ProfilerRecord} from '@/store/api'

import library from "./icons";
import {Store as AuthStore} from "@/modules/auth";
import {Routes, Store} from "@/modules/admin";
import {mapState} from "vuex";

library.add()


axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Access-Control-Allow-Origin'] = '*';
axios.defaults.headers.common['Access-Control-Allow-Credentials'] = true;


//
// const modules = [
//     "@/modules/admin",
//     "@/modules/auth",
// ].forEach(async (module)=> {
//     console.log(module)
//     const {Routes, Store} = await import(module);
//
//   console.log(Routes);
//   console.log(Store);
//   // store.registerModule('auth',AuthStore);
//   // router.addRoute()
//
//
// });

// console.log(modules)


const app = createApp(App)
    .use(store)
    .use(router)
    .use(VueAxios, axios)
;

// app.mixin({
//
//     mounted: async function () {
//         if (this.$parent == undefined && !this.$store.state.user.fetched) {
//             console.log("OMG WE MUST GO FETCH")
//             await this.$store.dispatch("user/fetch")
//             // this.$store.dispatch("session/loadSession");
//             // this.$store.commit("changeSessionLoaded");
//         }
//     },
// });


app.component("fa", FontAwesomeIcon);
app.provide('axios', app.config.globalProperties.axios);
app.mount("#app");

declare global {
    interface Window {
        profiler?: ProfilerRecord;
    }
}