import { defineComponent } from 'vue'
import {LocationQuery, LocationQueryValue} from 'vue-router'
export default defineComponent({
    data: () => ({
        state: <LocationQuery>{},
    }),
    mounted() {
        this.state = JSON.parse(JSON.stringify(this.$route.query))
    },
    methods:{
        setState(key:string, value:LocationQueryValue, emitterName:string|null=null): LocationQueryValue {
            this.state[key] = value
            this.$router.push({query: this.state})
            if (emitterName){
                this.$emit(emitterName,value);
            }
            return value;
        },
        getState(key:string): unknown {
            const t = this.state[key]

           console.log("FUCK YOU",t)
            return t;
        },
        removeState(key:string): void {
            delete this.state[key];
            this.$router.push({query: this.state})
        }
    }
})