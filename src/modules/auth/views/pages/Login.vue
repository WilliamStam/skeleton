<template>
    <div class="container">
        <div class="row">

            <div class="col-xl-5 col-lg-6 col-12 mx-auto my-5">

                <template v-if="user.id">
                    <div class="text-center mt-5">
                        <p>
                             <fa icon="check" class="fa-10x text-success"></fa>
                        </p>
                       <h3>
                           You are logged in.
                       </h3>
                    </div>


                    <router-link :to="{name: 'auth.logout'}" class="btn btn-primary w-100 my-5">Logout</router-link>
                </template>
                <form @submit.prevent="submit()" v-else>
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

                                    <button type="submit" class="btn btn-primary w-100 " :disabled="!active">Login</button>

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
        this.save = localStorage.getItem("username") ? true : false;
    },
    computed: {
        ...mapState("auth", {
            messages: state => state.messages,
            auth_active: state => state.active,
        }), ...mapState("user", {
            user: state => state.user,
        }),
        active() {
            console.log(this.user);
            if (this.user && this.user.id) {
                return false;
            }
            return this.auth_active;
        }

    },
    watch: {
        user(newValue, oldValue) {
            console.log(`Updating from ${oldValue} to ${newValue}`);

            // Do whatever makes sense now
            if (newValue === false) {
               this.$store.dispatch("auth/check");
            }
        },
    },
    methods: {
        submit() {
            if (this.save) {
                localStorage.setItem("username", this.username);
            } else {
                localStorage.removeItem("username");
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