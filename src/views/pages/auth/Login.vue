<template>
    <div class="container">
        <div class="row">

            <div class="col-xl-5 col-lg-6 col-12 mx-auto my-5">
                <form @submit.prevent="submit()">
                    <div class="card bg-white border shadow-lg my-5 ">
                        <div class="card-header">
                            <h1>Please Login</h1>
                        </div>


                        <template v-for="(message, index) in messages" :key="index">
                            <div class="alert text-center" :class="'alert-'+message.type">{{ message.message }}</div>
                        </template>

                        <div class="card-body">


                            <div class="form-group mb-3">
                                <label for="username">Email</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter email" v-model="username" required="" :disabled="!active">
                            </div>
                            <div class="form-group mb-3">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" v-model="password" required="" :disabled="!active">
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="form-check  mb-3">
                                <input class="form-check-input" type="checkbox" value="1" id="remember-me" v-model="save" :disabled="!active">
                                <label class="form-check-label" for="remember-me">
                                    Remember me
                                </label>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-12 ">
                                    <button type="submit" class="btn btn-primary w-100 " :disabled="!active">Login
                                    </button>
                                </div>
                            </div>


                        </div>
                    </div>
                </form>
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
        this.$store.dispatch("auth/check");
        this.username = this.$store.state.auth.username;
        this.save = localStorage.getItem('username') ? true : false;
    },
    computed: mapState("auth", {
        messages: state => state.messages,
        active: state => state.active,
    }),
    methods: {
        submit() {
            if (this.save){
                localStorage.setItem('username', this.username)
            } else {
                localStorage.removeItem('username')
            }
            if (this.username && this.password) {
                this.$store.dispatch("auth/login", {
                    username: this.username,
                    password: this.password
                });
            }
        },
    }

};
</script>
<style lang="scss">
.page-heading {
    text-align: center;
    font-weight: bold;
    padding: 4rem;
    background-color: #cccccc;
    -webkit-background-clip: text;
    -moz-background-clip: text;
    background-clip: text;
    color: transparent;
    text-shadow: rgba(255, 255, 255, 0.6) 0px 2px 2px;
    font-size: 7rem;
}

</style>