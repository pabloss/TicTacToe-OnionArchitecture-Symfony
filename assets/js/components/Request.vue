<template>
    <div>
        <button type="button" class="test" v-on:click="test()">Run Test</button>
        <p id="res">{{ res }}</p>
    </div>
</template>

<script>
export default {
    name: "request-buttons",
    data() {
        return {
            res: ""
        }
    },
    methods: {
        test: function () {
            console.log('vue-test');
            this.axios({ method: "GET", "url": "/deploy-and-test" }).then(result => {
                this.res = result.data.res;
            }, error => {
                console.error(error);
            });
        }
    },
}
</script>

<style scoped>

</style>
