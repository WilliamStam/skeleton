import axios, {
  AxiosError,
  // AxiosInstance,
  AxiosRequestConfig,
  AxiosResponse
} from "axios";
import {useStore} from "@/store";


const store = useStore();


axios.defaults.timeout = 2500;

const requestInterceptor = (req: AxiosRequestConfig): AxiosRequestConfig => {
  req.headers['request-startTime'] = new Date().getTime();
  console.log("request wroks");
  return req;
};

const successInterceptor = (response: AxiosResponse): AxiosResponse => {
  console.log("success wroks");

  const currentTime = new Date().getTime()
  const startTime = response.config.headers['request-startTime'];
  response.headers['request-duration'] = currentTime - startTime;

  const profiler = response.data.PROFILER;
  profiler.total.request = response.headers['request-duration'];


  store.dispatch("profiler/add", profiler);
  console.log(profiler);
  delete response.data.PROFILER;
  return response;
};

const errorInterceptor = (err: AxiosError) => {
  return Promise.reject(err);
};

axios.interceptors.request.use(requestInterceptor);
axios.interceptors.response.use(
  (res) => successInterceptor(res),
  (err) => errorInterceptor(err)
);



const api = {
    get(url:string): Promise<unknown> {


      return new Promise((resolve, reject) => {
        axios
          .get(url)
          .then((response: AxiosResponse) => {
            console.log("resolving data");
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