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
                                        <div
                                            class="item"
                                            :class="{ 'paid': payment.personPaid !== null }"
                                            v-for="payment in payments"
                                            :key="payment.key"
                                            @click="payItem(payment.amount, $event)"
                                        >
                                            <div class="content">
                                                <img :src="payment.personPaid !== null ? '/images/tick.svg' : '/images/plus.svg'">
                                                <p>{{ payment.item }}</p>
                                            </div>
                                            <div class="price">
                                                <i v-if="payment.personPaid === null">&euro;</i>
                                                {{ payment.personPaid !== null ? payment.personPaid : payment.amount.toFixed(2) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="amount-group d-flex">
                                        <h1 class="transaction-amount">
                                            <label for="amount">&euro;</label>
                                            <input type="number" step=".01" id="amount" v-model="amount.toFixed(2)">
                                        </h1>
                                        <div v-if="amount > 0" @click="resetAmounts" class="renew">
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
                amount: 0.00,
                payments: {
                    0: {
                        amount: 6.20,
                        item: 'Asian Nachos',
                        personPaid: null
                    },
                    1: {
                        amount: 2.60,
                        item: 'Lungo',
                        personPaid: 'Jan-Willem'
                    },
                    2: {
                        amount: 2.35,
                        item: 'Pils Hertog Jan',
                        personPaid: null
                    },
                    3: {
                        amount: 2.35,
                        item: 'Pils Hertog Jan',
                        personPaid: 'Anoniem'
                    }
                }
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
                        // console.log(this.data);
                    }
                });
            },
            payItem(decimal, event) {
                this.amount += decimal;
                event.currentTarget.className += ' paid';
                event.currentTarget.querySelector('.content img').src = '/images/tick.svg';
                event.currentTarget.querySelector('.price').innerHTML = 'Joost';

                // console.log(document.getElementsByClassName('item'));
            },
            resetAmounts() {
                this.amount = 0.00;
                this.fetch();
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
