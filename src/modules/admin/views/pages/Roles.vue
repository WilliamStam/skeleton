<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <h1>Admin Roles in modules</h1>
                <img alt="test image" src="/media/1246918803869.png?h=300&w=400"/>

            </div>
        </div>
    </div>
</template>
<script>
import {mapState} from "vuex";

export default {
    name: "AuthLogin",
    data: () => ({
        username: "",
        password: "",
        save: false
    }),


    mounted() {
        this.$store.dispatch("admin/roles/fetchList");

    },

    methods: {

    }

};
</script>
