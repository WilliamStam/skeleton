<template>
    <header id="page-menu">


        <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow">
            <div class="container">
                <a class="navbar-brand" href="#">Skeleton</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <router-link to="/" class="nav-link">Home</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link :to="{path:`/about/hhh`}" class="nav-link">About</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link :to="{path: '/contact'}" class="nav-link">Contact</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link :to="{path: '/test'}" class="nav-link">Testing</router-link>
                        </li>


                        <li class="nav-item">
                            <a class="nav-link" href="#">Link</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Dropdown
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="#">Action</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">Another action</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">Something else here</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled">Disabled</a>
                        </li>
                    </ul>
                     <ul class="navbar-nav ms-auto">
                        <li class="nav-item" v-if="user.id">
                            <router-link :to="{name: 'auth-logout'}" class="nav-link">Logout</router-link>
                        </li>
                        <li class="nav-item" v-else>
                            <router-link :to="{name: 'auth-login'}" class="nav-link">Login</router-link>
                        </li>
                     </ul>
                </div>
            </div>
        </nav>
    </header>
</template>
<script>
import {mapState} from "vuex";

export default {
    name: "PageMenu",
     computed: mapState("user", {
        user: state => state.user,
        permissions: state => state.permissions,
    }),
};
</script>
<style lang="scss">
#page-menu {

    nav {
        padding: 0;
border-bottom:1px solid $border-color;
        .nav-item {
            .nav-link {

            }

            .router-link-active {
                background: $primary;
                color: $white;
            }
        }
    }

}

</style>