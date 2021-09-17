<template>
    <div class="container">
        <div class="row">

            <div class="col-xl-5 col-lg-6 col-12 mx-auto my-5">
                <form @submit.prevent="submit()">
                <div class="card bg-white border shadow-lg my-5 ">
                    <div class="card-header">
                        <h1>Please Login</h1>
                    </div>

                    <div class="alert alert-warning text-center my-0">
                        Use your full email address and network password (same as you use to log into windows)
                    </div>

                    <div class="card-body">



                        <div class="form-group mb-3">
                            <label for="username">Email</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter email"  v-model="username" required="">
                        </div>
                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password"  v-model="password" required="">
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12 ">
                                <button type="submit" class="btn btn-primary w-100 ">Login</button>
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
export default {
    name: "AuthLogin",
     data: () => ({
        // hold the current selected values
        messages: [],
         username: "",
         password:""
    }),
    methods: {
        // add the forms selected stuff to the global store
        submit() {
            // if no "id" on it we give it an id

            this.messages = this.$store.dispatch("user/login",{username: this.username, password: this.password});
        },
    }

};
</script>
<style lang="scss">
.page-heading {
    text-align: center;
    font-weight: bold;
    padding: 4rem;
    background-color: #ccc;
    -webkit-background-clip: text;
    -moz-background-clip: text;
    background-clip: text;
    color: transparent;
    text-shadow: rgba(255, 255, 255, 0.6) 0px 2px 2px;
    font-size: 7rem;
  }

</style>