<template>
    <div class="alert">
        html with some synthetic sugar data1: {{ data1 }} | data2: {{ data2 }}

    </div>
    <div>
        <button type="button" @click="myMethodName">Alert me to the value of 2</button>
    </div>
</template>
<script>
// normal javascript object
export default {
    name: "ComponentName",
    data: () => ({
        data1: "the value",
    }),
    // a lifecycle hook
    mounted() {
        this.data1 = "the value after mounting"
    },
    computed: {
        // used just like a data item but its a function
        data2() {
            return this.data1 + " - added data2 ";
        }
    },
    methods: {
        myMethodName(){
            alert(this.data2)
        }
    }
};
</script>
<style>
.alert {
    background: rgba(250,0,0,0.2);
}
</style>