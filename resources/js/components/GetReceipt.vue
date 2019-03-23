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
                                        <img src="https://bunq.me/assets/img/avatar-placeholder.svg"
                                             class="rounded-circle" alt="Avatar">
                                        <p class="first-name">Joost</p>
                                    </div>
                                    <h2 class="mt-3">Je mag altijd meer betalen!</h2>
                                    <div class="list">
                                        <div
                                            class="item"
                                            :class="{
                                                'paid': names[index] !== 'Nog niet betaald...',
                                                'selected': selectedIndexes.includes(index)
                                            }"
                                            v-for="(payment, index) in data"
                                            @click="addToPaymentSelection(payment, index)"
                                        >
                                            <div class="content">
                                                <img :src="getIcon(names[index])" alt="Plus">
                                                <p>{{ names[index] }}</p>
                                            </div>
                                            <div class="price">
                                                <i>&euro;</i> {{ payment.toFixed(2) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="amount-group d-flex">
                                        <h1 class="transaction-amount">
                                            <label for="amount">&euro;</label>
                                            <input type="number" step=".01" id="amount"
                                                   v-model="amount.toFixed(2)">
                                        </h1>
                                        <div v-if="amount > 0" @click="resetEverything" class="renew">
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
                        <button :class="{ 'enough': amount > 0 }" :disabled="!amount > 0">Betaal veilig met iDEAL
                        </button>
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
                response: [],
                amount: 0.00,
                names: [
                    'Nog niet betaald...',
                    'Isabelle',
                    'Floris',
                    'Olivier',
                    'Feline',
                    'Esther',
                    'Nog niet betaald...',
                    'Floris',
                    'Nog niet betaald...',
                    'Isabelle',
                    'Feline',
                    'Nog niet betaald...',
                ],
                selectedIndexes: []
            }
        },
        mounted() {
            this.fetch();
        },
        methods: {
            fetch() {
                axios.get(`/api/getjson/${this.hash}`).then((data) => {
                    if (data.status === 200) {
                        this.response = data.data['responses'][0]['fullTextAnnotation']['text'].split(/\r?\n/);
                    }
                });
            },
            getIcon(status) {
                if (status !== 'Nog niet betaald...') {
                    return '/images/tick.svg';
                }
                return '/images/plus.svg';
            },
            addToPaymentSelection(payment, index) {
                this.amount += payment;
                this.selectedIndexes.push(index);

                console.log(this.selectedIndexes);
            },
            resetEverything() {
                this.amount = 0.00;
                this.fetch();
                this.selectedIndexes = [];
            }
        },
        computed: {
            hashImgUrl() {
                return `${this.bucketUrl}${this.hash}.png`;
            },
            data() {
                const pattern = RegExp('^[0-9]+(\,[0-9]{1,2})?$');
                return this.response.filter((data) => {
                    return pattern.test(data);
                }).map((data) => {
                    return parseFloat(data.replace(',', '.'));
                });
            }
        },
        destroyed() {
            const tracks = this.mediaStream.getTracks();
            tracks.map(track => track.stop())
        }

    }

</script>

<style scoped>

</style>
