<template>
    <div>

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
                mediaStream: null,
                amount: 0.00
            }
        },
        mounted() {
            navigator.mediaDevices.getUserMedia({video: true})
                .then(mediaStream => {
                    this.mediaStream = mediaStream
                    this.$refs.video.srcObject = mediaStream
                    this.$refs.video.play()
                })
                .catch(error => console.error('getUserMedia() error:', error));
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
            },
            checkAmount() {
                return this.amount > 0;
            },
            setAmount(amount) {
                this.amount = amount.toFixed(2);
            }
        },
        destroyed() {
            const tracks = this.mediaStream.getTracks();
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
