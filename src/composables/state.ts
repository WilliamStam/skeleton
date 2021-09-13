import {ref} from "vue";
// import { useRouter, useRoute } from 'vue-router'

export default {
    setup() {
        const state = ref({})


        const set = (key: string, value: string): string => {
            console.log("setting query", key, "to", value)
            // const updated_state = state.value;
            // updated_state[key] = value;
            // state.value = updated_state
            // router.push({
            //   name: 'search',
            //   query: {
            //     ...route.query,
            //   },
            // })

            return value;
        }

        return {
            state,
            set
        }
    },
}