import axios, {AxiosInstance,AxiosRequestConfig} from 'axios'
import md5 from '@/utilities/md5';
import {useStore} from '@/store';

// interface getDataCallable {
//     (name: object): object;
// }

const CancelToken = axios.CancelToken;
const source = CancelToken.source();
const store = useStore();

interface AxiosCustomRequestConfig extends AxiosRequestConfig {
    key?: string;
  }


const axios_instance = (config={}) => {
    const instance = axios.create(config);
    instance.interceptors.request.use(function (config: AxiosCustomRequestConfig) {

        console.log("axios key",config.key);


        return {
            ...config,
            cancelToken: new CancelToken((cancel) => cancel('Cancel repeated request'))
          };
    }, function (error) {
        return Promise.reject(error);
    });

    // Add a response interceptor
    instance.interceptors.response.use(function (response) {
        store.dispatch("profiler/add", response.data.PROFILER);
        delete response.data.PROFILER;
        return response;
    }, function (error) {
        return Promise.reject(error);
    });

    return instance;
}

const getInstance = (key: string): AxiosInstance => {

    // if (store.getters["api/getActive"]){
    //     store.getters["api/getActive"].filter((item:{key:string,instance:AxiosInstance}) =>  key == item.key).forEach((item:{key:string,instance:AxiosInstance})=>{
    //         item.instance.cancelToken.cancel();
    //     })
    // }
    // console.log("le getters",store.getters["api/getActive"].filter((item:{key:string,instance:AxiosInstance}) =>  key == item.key));

    const instance = axios_instance({
        key: key
    });

    const payload: {key: string, instance: AxiosInstance} = {
        key: key,
        instance: instance
    };
    store.dispatch("api/add", payload);


    return instance;
}




const api = {
    async get(url = "", params = {}, name = "") {

        if (!name) {
            name = JSON.stringify([url, params])
        }
        const key = md5(name);


        console.log(name, key)
        const instance = getInstance(key);


        const {data} = await instance.get(url).finally(() => {
            store.dispatch("api/remove", key);
        });
        return data;

    }


}

export default api;


// export default instance;

//
//
// export default {
//     methods: {
//         api(url = "", params = {}) {
//             // if url starts with a @ then check the STORE for path urls. apply params to the matching param names
//             // if url starts with a @ then call a special api endpoint that does the routing / redirecting.
//             if (url.substr(0, 1) == "@") {
//                 url = `/${url}`;
//             }
//             // url = `${api_base}${url}`;
//
//             const store = useStore();
//
//
//             return axios
//                 .get(url, {
//                     responseType: "json",
//                 })
//                 .then((response: { data: any }) => {
//                     store.dispatch("profiler/add", response.data.PROFILER);
//                     delete response.data.PROFILER;
//                     return response.data;
//                 });
//         },
//
//     }
// }