<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <h1>About</h1>
                <img alt="test image" :src="media('/media/119068453133.jpg?h=300&w=400')"/>
                <div>[{{ $route.params.id }}]</div>
                <div>[{{ id }}]</div>
            </div>
        </div>

    </div>

</template>
<script>
export default {
    props: ["id"],
};
</script>