<template>
    <div>
        <div class="grids">
            <ul class="grid-9">
                <li v-for="n in 9">
                    <a class="wide" v-bind:id="'tile_' + ((n-1) - ((n-1) % 3)) / 3 + '_' + (n-1) % 3"
                       @click="playerTurn((counter%2) ? symbols[0]: symbols[1], ((n-1) - ((n-1) % 3)) / 3, (n-1) % 3 ); counter++">{{ res[n-1] }}</a>
                </li>
            </ul>
        </div>
        <button value="Reset" id="reset" @click="reset();">Reset</button>
    </div>
</template>

<script>
    export default {
        name: "PlayerTurn",
        data: function()
        {
            return{counter: 1, symbols: ['X', '0'], res: []};
        },
        methods: {
            playerTurn: function (symbol, x, y) {
                console.log('turn with symbol: ' + symbol + " " + x + " " + y);
                let vm = this;
                this.axios({ method: "GET", "url": "/game/get-tile/"+symbol+"/"+x+","+y }).then(result => {
                    if(result.status === 200){
                        vm.res = result.data;
                        console.log(result.data);
                    }
                }, error => {
                    console.error(error);
                });
            },
            reset: function () {
                this.axios({ method: "GET", "url": "/game/reset"}).then(result => {
                    let vm = this;
                    if(result.status === 200){
                        vm.res = result.data;
                        console.log(result.data);
                    }
                }, error => {
                    console.error(error);
                });
            }
        },
        created() {
            this.axios({ method: "GET", "url": "/api/game/"}).then(result => {
                this.res = result.data;
                if(result.status === 200){
                    console.log(this.res);
                }
            }, error => {
                console.error(error);
            });
        },
        // watch is important to react on every response change from backend
        watch: {
            res: function (newRes, oldRes) {
                this.res = newRes;
            }
        }
    }
</script>

<style scoped>
    .grids {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        grid-template-rows: 1fr 1fr 1fr;
        width: 100vmin;
        height: calc(100vh - 4em);
        grid-gap: 2em;
        margin: 0 auto;
    }
    ul {
        display: grid;
        /*grid-template-columns: repeat(2, 1fr 1fr 3fr 5fr);*/
        grid-template-columns: 1fr 1fr 1fr;
        grid-template-rows: 1fr 1fr 1fr;
        /*grid-auto-rows: minmax(24vw, 1fr);*/
        /*grid-template-rows: repeat(autofit, 1fr 1fr 3fr 5fr);*/
        /*grid-template-rows: repeat(autofit, 3fr 2fr);*/
        /*grid-template-columns: repeat(autofit, minmax(120px, 1fr));*/
        /*grid-template-columns: repeat(2, 1fr 1fr 3fr 5fr);*/
        /*grid-template-columns: [duck] 1fr [duck] 1fr [goose];*/
        grid-gap: .5em;
        width: 100%;
        height: 100%;
        background: white;
        padding: .5em;
        border: .2em solid #91a7ff;
        /*box-shadow: 0 3px 6px rgba(54,79,199,0.05), 0 7px 14px rgba(54,79,199,0.1);*/
        box-sizing: border-box;
        border-radius: .5em;
        transition: all 0.3s ease;
    }
    li {
        /*border: .25em solid #3b5bdb;*/
        background: #748ffc;
        box-sizing: border-box;
        /*border-radius: .5em;*/
        list-style-type: none;
    }
    a.wide {
        display:block;
        height: 100%;
        width: 100%;
    }

    .light {
        opacity: .33;
    }
</style>
