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
