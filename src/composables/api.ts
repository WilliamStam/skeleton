import axios, {
    AxiosError,
    // AxiosInstance,
    AxiosRequestConfig,
    AxiosResponse
} from "axios";


import {useStore} from "@/store";
import {objectToQueryString} from "@/utilities/serialize";
import md5 from "@/utilities/md5";

const store = useStore();

export interface AxiosRequestAPIConfig extends AxiosRequestConfig {
    key?: string
}


interface AxiosResponseAPI extends AxiosResponse {
    config: AxiosRequestAPIConfig
}

axios.defaults.timeout = 2500;

const requestInterceptor = (req: AxiosRequestAPIConfig): AxiosRequestAPIConfig => {
    req.headers['request-startTime'] = new Date().getTime();
    req.headers['session'] = "";

    // console.log("HEADERS",req.headers);

    if (!req.key){
        req.key = md5(req.url + "|" + req.method + "|" + req.data);
    }

    // const previous_requests = store.getters["api/getList"].map((item:ApiCall) => {
    //     if (item.key == req.key){
    //         // console.log("MUST CANCEL THIS RESPONSE",item)
    //     }
    //
    // });


    // console.log("PREVIOUS REQUESTS",previous_requests);


    // console.log("check if ",req.key,"is active and cancel it if it is");

     store.dispatch("api/addActive", {
         key: req.key,
         instance: this,
         config: req
     });

    // console.log("request key",req.key,req);
    return req;
};

const responseInterceptor = (response: AxiosResponseAPI): AxiosResponseAPI => {
    // console.log("success wroks");

    const currentTime = new Date().getTime()
    const startTime = response.config.headers['request-startTime'];
    response.headers['request-duration'] = currentTime - startTime;

    if ("PROFILER" in response.data){
        const profiler = response.data.PROFILER;
        profiler.total.request = response.headers['request-duration'];
        store.dispatch("api/addProfiler", profiler);
        // console.log(profiler);
        delete response.data.PROFILER;
    }

    store.dispatch("api/removeActive",response.config.key);


    // console.log("remove ",response.config.key,"from active")

    return response;
};

const errorInterceptor = (err: AxiosError) => {
    const config: AxiosRequestAPIConfig = err.config;


    store.dispatch("api/removeActive",config.key);
    return Promise.reject(err);
};

axios.interceptors.request.use(requestInterceptor);
axios.interceptors.response.use(
    (res) => responseInterceptor(res),
    (err) => errorInterceptor(err)
);

type EmptyKeyValueObject = {
    [key: string]: number | string | boolean,
}

const api = {
    get(url: string, params: EmptyKeyValueObject = {}, options: AxiosRequestAPIConfig = {}): Promise<unknown> {

        if (url.includes("?")) {
            url += "&" + objectToQueryString(params)
        } else {
            url += "?" + objectToQueryString(params)
        }

        // axios_config.options = options;

        return new Promise((resolve, reject) => {
            axios
                .get(url, options)
                .then((response: AxiosResponseAPI) => {
                    resolve(response.data);
                })
                .catch((error: AxiosError) => {
                    reject(error);
                });
        });
    },
    post(url: string, params: EmptyKeyValueObject = {}, options: AxiosRequestAPIConfig = {}): Promise<unknown> {


        return new Promise((resolve, reject) => {
            axios
                .post(url,params, options)
                .then((response: AxiosResponseAPI) => {
                    resolve(response.data);
                })
                .catch((error: AxiosError) => {
                    reject(error);
                });
        });
    }
}
export default api;

// desired usage:
// const data = await api.get("/url",{"id":1},{ loading: true, name: "request-name"}); -> /url?id=1
// const data = await api.get("/url?tada=foo",{"id":1},{ loading: true, name: "request-name"}); -> /url?id=1&tada=foo
// const data = await api.post("/url/post",{"name":"john","surname": "Smith"},{ loading: true, name: "request-name"}); -> /url/post {....}

// if the request-name already has an ajax request out then to cancel the previous 1.
// if no name option specified (request name in the options)  then stringify the url and params together and md5 it
// on request to add a record to the store. with all the options, and cancel token and key.
//  a count of the vuex store shows how many active requests there are (so if store.length){ show loading screen if option.loading is enabled (off by default)
// on a call to the api to "mangle" the response a bit by removing the PROFILER key if it exists and adding it to the profiler store list