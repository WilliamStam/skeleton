import { useRouter, useRoute } from 'vue-router'

const state = {
    // const router = useRouter()
    // const route = useRoute()

    set(key:string,value:string){
      const router = useRouter()
      const route = useRoute()

       console.log(router)
      console.log(route)
      const q = route.query;

      console.log(q)
      q[key] = value



      router.push({
        query: q
      })
      // console.log("ROUTER",router)
      // console.log("set state",key,"to value",value);
      // router.push({
      //   query: {
      //     a: "sick",
      //   },
      // })

        return value;
    }


}
export default state;