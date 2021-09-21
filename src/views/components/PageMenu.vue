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
                            <router-link :to="{name: 'home'}" class="nav-link">Home</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link :to="{path:`/about/hhh`}" class="nav-link">About</router-link>
                        </li>


                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown border-start" :class="{'active': routeNameStartsWith('admin.')}">
                                <a
                                    class="nav-link dropdown-toggle"
                                    href="#"
                                    id="menu-admin-dropdown"
                                    role="button"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"

                                >
                                    <fa icon="cogs"></fa>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="menu-admin-dropdown">

                                    <li>
                                        <router-link :to="{name: 'admin.roles'}" class="dropdown-item">Roles
                                        </router-link>
                                    </li>
                                    <li>
                                        <router-link :to="{name: 'admin.roles.categories'}" class="dropdown-item">Roles
                                            Categories
                                        </router-link>
                                    </li>

                                </ul>
                            </li>
                        <template v-if="user.id">
                            <li class="navbar-text px-2">
                                {{ user.name }}
                            </li>


                            <li class="nav-item">
                                <router-link :to="{name: 'auth.logout'}" class="nav-link">Logout</router-link>
                            </li>
                        </template>
                        <template v-else>
                            <li class="nav-item">
                                <router-link :to="{name: 'auth.login'}" class="nav-link">Login</router-link>
                            </li>
                        </template>


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
    mounted() {
        this.$store.dispatch("user/fetch");
    },
    computed: {
        ...mapState("user", {
            user: state => state.user,
            permissions: state => state.permissions,
        })
    },
    methods: {
        routeNameStartsWith(string) {
            if (this.$route && this.$route.name) {
                return this.$route.name.substr(0, string.length) === string;
            }
            return false;
        }
    }
};
</script>
<style lang="scss">
#page-menu {

    nav {
        padding: 0;
        border-bottom: 1px solid $border-color;

        .nav-item {
            &.active {
                .nav-link {
                    background: $primary;
                    color: $white;
                }
            }

            .router-link-active {
                background: $primary;
                color: $white;
            }
        }
    }

}

</style>