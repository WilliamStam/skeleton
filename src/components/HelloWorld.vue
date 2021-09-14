<template>


    <div>
        <h1>
            active requests: [{{ loadingRequests }}]
        </h1>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">

                <button class="nav-link" @click.prevent="setActive('home')" :class="{ active: isActive('home') }" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">


                    Home
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" @click.prevent="setActive('logs')" :class="{ active: isActive('logs') }" id="home-logs" data-bs-toggle="tab" data-bs-target="#logs" type="button" role="tab" aria-controls="logs" aria-selected="true">
                    Logs
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" @click.prevent="setActive('profile')" :class="{ active: isActive('profile') }" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">
                    Profile
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" @click.prevent="setActive('contact')" :class="{ active: isActive('contact') }" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">
                    Contact
                </button>
            </li>
        </ul>
        <div v-if="response">
            <div>tab: {{ response.tab }}</div>
            <div>date: {{ response.date }}</div>
            <!--            only response.version shows in the test.json. the api has other options but for packaging purposes...-->
            <div>version: {{ response.version }}</div>
            <div>random: {{ response.r }}</div>
            <div>get:
                <template v-for="(v,k) in response.get" :key="k">
                    <div class="ms-4">{{ k }}: {{ v }}</div>

                </template>
            </div>
            <div>post:
                <template v-for="(v,k) in response.post" :key="k">
                    <div class="ms-4">{{ k }}: {{ v }}</div>
                </template>
            </div>

            <p>
                <a class="btn link mt-4" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                    Headers
                </a>
            </p>
            <div class="collapse" id="collapseExample">
                <div class="card card-body">
                    <div>headers:
                        <template v-for="(v,k) in response.headers" :key="k">
                            <div class="ms-4">{{ k }}: {{ v }}</div>

                        </template>
                    </div>
                </div>
            </div>


        </div>
        <div v-else>Loading...</div>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade" :class="{ 'active show': isActive('home') }" id="home" role="tabpanel" aria-labelledby="home-tab">
                home
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%"></div>
                </div>

            </div>
            <div class="tab-pane fade" :class="{ 'active show': isActive('logs') }" id="logs" role="tabpanel" aria-labelledby="home-logs">
                logs

                <div v-if="response">
                    <template v-for="log in response.logs" :key="log.id">
                        <div class="row border-bottom">
                            <div class="col-1">{{ log.id }}</div>
                            <div class="col-2">{{ log.datetime }}</div>
                            <div class="col-4">{{ log.log }}</div>
                            <div class="col">{{ log.context }}</div>

                        </div>
                    </template>

                </div>
                <div v-else>
                    loading still
                </div>

            </div>
            <div class="tab-pane fade" :class="{ 'active show': isActive('profile') }" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                profile
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 88%"></div>
                </div>
            </div>
            <div class="tab-pane fade" :class="{ 'active show': isActive('contact') }" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                contact

                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 25%"></div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <!--        <fa icon="user-secret" style="color:red;"></fa>-->
        <!--        <fa icon="download" style="color:green;"></fa>-->
        <!--        <fa icon="grin-squint-tears" style="color:blue;"></fa>-->
    </div>

    <div>State: {{ state }}</div>
    <div>
        <button type="button" class="btn btn-primary">Primary</button>
        <button type="button" class="btn btn-secondary">Secondary</button>
        <button type="button" class="btn btn-success">Success</button>
        <button type="button" class="btn btn-danger">Danger</button>
        <button type="button" class="btn btn-warning">Warning</button>
        <button type="button" class="btn btn-info">Info</button>
        <button type="button" class="btn btn-light">Light</button>
        <button type="button" class="btn btn-dark">Dark</button>

        <button type="button" class="btn btn-link">Link</button>
    </div>
</template>
<script>
/* eslint-disable */
import {
    Modal,
    Tab
} from "bootstrap";
import api from "@/composables/api";
import {watch} from "vue";
import {mapGetters} from "vuex";

// import ApiCallModel from "@/models/system/api";

export default {
    name: "App",
    data: () => ({
        modal: null,
        tabs: null,
        activeItem: "home",
        response: undefined,
    }),
    mounted() {
        this.tabs = new Tab(this.$refs.myTab);

        watch(() => this.activeItem, (newVal) => {
            this.getData();

        });
        this.getData();

    },
    computed: {
        loadingRequests() {
            return this.$store.getters["api/showLoading"];
        }
    },
    methods: {


        isActive(menuItem) {
            return this.activeItem === menuItem;
        },
        setActive(menuItem) {
            this.activeItem = menuItem;
        },


        async getData() {

            // just forcing the response to be empty while it loads new content
            this.response = undefined;


            if (this.activeItem == "contact") {
                this.response = await api.post(`/api/test/tab/${this.activeItem}?fish=grrr`, {
                    r: Math.random(),
                    y: "p"
                }, {
                    loading: true
                });
            } else {
                this.response = await api.get(`/api/test/tab/${this.activeItem}?fish=cakes`, {
                    r: Math.random(),
                    y: "g"
                }, {
                    key: "request-" + this.activeItem,
                    headers: {
                        "x-sexy": "ola1"
                    },
                    loading: true
                });
            }

            // this.response = await FetchApi(`/api/test/tab/${this.activeItem}`,{}, "test");
            // this.response = await FetchApi(`/api/test/tab/${this.activeItem}`,{});
            // this.response = await FetchApi(`/api/test/tab/${this.activeItem}`,{});

        }
    }
};
</script>