<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="title">Bunq.me Receipt Scanner</h1>
                </div>
                <div class="col-12 flex-container">
                    <div class="card">
                        <div class="container-fluid p-0">
                            <div class="row">
                                <div class="col-6">
                                    <h2>Stuur een betaling naar:</h2>
                                    <div class="avatar">
                                        <img src="https://bunq.me/assets/img/avatar-placeholder.svg" class="rounded-circle" alt="Avatar">
                                        <p class="first-name">Joost</p>
                                    </div>
                                    <h2 class="mt-3">Je mag altijd meer betalen!</h2>
                                    <div class="list">
                                        <div class="item" @click="amount += 6.20">
                                            <div class="content">
                                                <img src="../../img/plus.svg" alt="Plus">
                                                <p>Asian Nachos</p>
                                            </div>
                                            <div class="price">
                                                <i>&euro;</i> 6,20
                                            </div>
                                        </div>
                                        <div class="item paid">
                                            <div class="content">
                                                <img src="../../img/tick.svg" alt="Tick">
                                                <p>Lungo</p>
                                            </div>
                                            <div class="price">
                                                Jan-Willem
                                            </div>
                                        </div>
                                        <div class="item" @click="amount += 2.35">
                                            <div class="content">
                                                <img src="../../img/plus.svg" alt="Plus">
                                                <p>Pils Hertog Jan</p>
                                            </div>
                                            <div class="price">
                                                <i>&euro;</i> 2,35
                                            </div>
                                        </div>
                                        <div class="item paid">
                                            <div class="content">
                                                <img src="../../img/tick.svg" alt="Tick">
                                                <p>Pils Hertog Jan</p>
                                            </div>
                                            <div class="price">
                                                Anoniem
                                            </div>
                                        </div>
                                    </div>
                                    <div class="amount-group d-flex">
                                        <h1 class="transaction-amount">
                                            <label for="amount">&euro;</label>
                                            <input type="number" step=".01" id="amount" v-model="amount.toFixed(2)">
                                        </h1>
                                        <div v-if="amount > 0" @click="amount = 0.00" class="renew">
                                            <img src="../../img/renew.svg" alt="renew">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <img :src="hashImgUrl" class="w-100" alt="Receipt">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bottom-part">
                        <button :class="{ 'enough': amount > 0 }" :disabled="!amount > 0">Betaal veilig met iDEAL</button>
                        <p>Betaal in plaats daarvan met <a href="https://www.klarna.com/sofort/">Sofort</a></p>
                    </div>
                </div>
            </div>
        </div>

        <!--<img :src="hashImgUrl" alt="Receipt">-->
        <!--<template v-if="data.responses[0]">-->
            <!--{{ data.responses[0].textAnnotations[0].description }}-->
        <!--</template>-->
    </div>

</template>

<script>
    export default {
        name: "GetReceipt",
        props: ['hash'],
        data() {
            return {
                bucketUrl: 'https://bunq-hackathon.ams3.digitaloceanspaces.com/',
                data: {
                    responses: []
                },
                amount: 0.00
            }
        },
        mounted() {
            this.fetch();
        },
        methods: {
            fetch() {
                axios.get(`/api/getjson/${this.hash}`).then((data) => {
                    if (data.status === 200) {
                        this.data = data.data;
                        var responses = this.data['responses'][0]['textAnnotations'];
                        console.log(responses);

                        responses.forEach(responseVisualizer);
                        function responseVisualizer(responseItem) {
                            var responseContent = responseItem['description'];
                            var responseLocation = responseItem['__ob__'];
                            console.log(responseContent);
                            console.log(responseLocation);
//                            console.log(responseItem);

                        }
//                        console.log(responses);

                    }
                });
            }
        },
        computed: {
            hashImgUrl() {
                return `${this.bucketUrl}${this.hash}.png`;
            },
        },
        destroyed() {
            const tracks = this.mediaStream.getTracks();
            tracks.map(track => track.stop())
        }

    }

</script>

<style scoped>

</style>
