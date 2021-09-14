import { createApp } from "vue";
import App from "./App.vue";
import router from "@/router";
import store from "@/store";
import axios from 'axios'
import VueAxios from 'vue-axios'
import '@/assets/css/styles.scss'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import {ProfilerRecord} from '@/store/api'

import library from "./icons";
library.add()


axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Access-Control-Allow-Origin'] = '*';
axios.defaults.headers.common['Access-Control-Allow-Credentials'] = true;


const app = createApp(App)
    .use(store)
    .use(router)
    .use(VueAxios, axios)
;



app.component("fa",FontAwesomeIcon);
app.provide('axios', app.config.globalProperties.axios);
app.mount("#app");

declare global {
  interface Window {
    profiler?: ProfilerRecord;
  }
}