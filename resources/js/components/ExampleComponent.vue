<template>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="title">Bunq.me Receipt Scanner</h1>
            </div>
            <div class="col-12 d-flex justify-content-center">
                <div class="card">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-6">
                                <h2>Stuur een betaling naar:</h2>
                                <div class="avatar">
                                    <img src="https://bunq.me/assets/img/avatar-placeholder.svg" class="rounded-circle" alt="Avatar">
                                    <p class="first-name">Joost</p>
                                </div>
                                <h2 class="mt-3">Je mag altijd meer betalen!</h2>
                                <div class="list">
                                    <div class="item">
                                        <img src="../../img/plus.svg" alt="Plus">
                                        <p>Asian Nachos</p>
                                    </div>
                                    <div class="item paid">
                                        <img src="../../img/tick.svg" alt="Tick">
                                        <p>Lungo</p>
                                    </div>
                                    <div class="item">
                                        <img src="../../img/plus.svg" alt="Plus">
                                        <p>Pils Hertog Jan</p>
                                    </div>
                                    <div class="item paid">
                                        <img src="../../img/tick.svg" alt="Tick">
                                        <p>Pils Hertog Jan</p>
                                    </div>
                                </div>
                                <h1 class="transaction-amount">
                                    <label for="amount">&euro;</label>
                                    <input type="text" id="amount" value="0,00">
                                </h1>
                            </div>
                            <div class="col-6">
                                <img src="../../img/IMG_5923.jpg" class="w-100" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {}
    }
</script>
