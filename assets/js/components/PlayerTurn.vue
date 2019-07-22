<template>
    <ul>
        <li  v-for="n in 10">
        <a @click="playerTurn('X', ((n) - ((n) % 3)) / 3, (n) % 3 )">XXX</a>

        </li>
    </ul>

</template>

<script>
    export default {
        name: "PlayerTurn",
        methods: {
            playerTurn: function (symbol, x, y) {
                console.log('turn with symbol: ' + symbol + " " + x + " " + y);
                this.preventDefault();
                this.axios({ method: "GET", "url": "/game/get-tile/"+symbol+"/"+x+","+y }).then(result => {
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
