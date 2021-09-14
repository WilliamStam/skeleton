<template>

    <div class="loadingmask" v-if="loadingRequests">
        <div class="loading">
            <div class="spinner-border" role="status">
  <span class="visually-hidden">Loading...</span>
</div>
            <div class="spinner">
                <div class="rect1">L</div>
                <div class="rect2">o</div>
                <div class="rect3">a</div>
                <div class="rect4">d</div>
                <div class="rect5">i</div>
                <div class="rect6">n</div>
                <div class="rect7">g</div>
            </div>
        </div>
    </div>

</template>

<style scoped lang="scss">

.spinner {
    margin: 20px auto;
    width: 120px;

    text-align: center;
    font-size: 15px;
}

.spinner > div {
    height: 100%;
    width: 8px;
    display: inline-block;

    -webkit-animation: sk-stretchdelay 1.2s infinite ease-in-out;
    animation: sk-stretchdelay 1.2s infinite ease-in-out;
}

.spinner .rect2 {
    -webkit-animation-delay: -1.1s;
    animation-delay: -1.1s;
}

.spinner .rect3 {
    -webkit-animation-delay: -1.0s;
    animation-delay: -1.0s;
}

.spinner .rect4 {
    -webkit-animation-delay: -0.9s;
    animation-delay: -0.9s;
}

.spinner .rect5 {
    -webkit-animation-delay: -0.8s;
    animation-delay: -0.8s;
}

.spinner .rect6 {
    -webkit-animation-delay: -0.7s;
    animation-delay: -0.7s;
}

.spinner .rect7 {
    -webkit-animation-delay: -0.6s;
    animation-delay: -0.6s;
}

@-webkit-keyframes sk-stretchdelay {
    0%, 40%, 100% {
        -webkit-transform: scaleY(1.0)
    }
    20% {
        -webkit-transform: scaleY(1.5)
    }
}

@keyframes sk-stretchdelay {
    0%, 40%, 100% {
        transform: scaleY(1.0);
        -webkit-transform: scaleY(1.0);
    }
    20% {
        transform: scaleY(1.5);
        -webkit-transform: scaleY(1.5);
    }
}

.loadingmask {
    background: rgba(250, 250, 250, 0.7);
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 350000;
    display: block;


    .loading {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        margin-top: -30px;
        text-align: center;


    }
}

</style>

<script>

export default {
    name: "LoadingMask",
    computed: {
        loadingRequests() {
            return this.$store.getters["api/showLoading"];
        }
    },
};
</script>
