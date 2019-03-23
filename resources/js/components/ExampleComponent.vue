<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="title">Bunq.me Receipt Scanner</h1>
                </div>
                <div class="col-12 d-flex justify-content-center">
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
                                        <div class="item">
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
                                        <div class="item">
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
        <!--<div class="camera-modal">-->
            <!--<video ref="video" class="camera-stream"/>-->
            <!--<div class="camera-modal-container">-->
                <!--<span @click="capture"-->
                      <!--class="take-picture-button take-picture-button mdl-button mdl-js-button mdl-button&#45;&#45;fab mdl-button&#45;&#45;colored">-->
                  <!--<i class="material-icons">camera</i>-->
                <!--</span>-->
            <!--</div>-->
        <!--</div>-->
    </div>
</template>

<script>
    export default {
        data() {
            return {
                mediaStream: null
            }
        },
        mounted() {
            navigator.mediaDevices.getUserMedia({video: true})
                .then(mediaStream => {
                    this.mediaStream = mediaStream
                    this.$refs.video.srcObject = mediaStream
                    this.$refs.video.play()
                })
                .catch(error => console.error('getUserMedia() error:', error))
        },
        methods: {
            capture() {
                const mediaStreamTrack = this.mediaStream.getVideoTracks()[0]
                const imageCapture = new window.ImageCapture(mediaStreamTrack)
                return imageCapture.takePhoto().then(blob => {
                    const data = new FormData();
                    data.append('receipt', blob, 'receiptImage');
                    console.log(data);
                    axios.post('/api/receipt', data, {
                        headers: {
                            'Content-Type': `multipart/form-data; boundary=${data._boundary}`,
                        },
                        timeout: 30000,
                    }).then((data) => {
                        console.log(data);
                    });
                });
            }
        },
        destroyed() {
            const tracks = this.mediaStream.getTracks()
            tracks.map(track => track.stop())
        }
    }
</script>
<style scoped>
    .camera-modal {
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        position: absolute;
        background-color: white;
        z-index: 10;
    }

    .camera-stream {
        width: 100%;
        max-height: 100%;
    }

    .camera-modal-container {
        position: absolute;
        bottom: 0;
        width: 100%;
        align-items: center;
        margin-bottom: 24px;
    }

    .take-picture-button {
        position: fixed;
        right: 24px;
        bottom: 90px;
        z-index: 5;
        display: flex;
    }
</style>
