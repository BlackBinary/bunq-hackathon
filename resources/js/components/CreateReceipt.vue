<template>
    <div class="flex-container">
        <div class="card">
            <div class="camera-modal">
                <video ref="video" class="camera-stream" />
            </div>
            <div class="bottom-part custom-flex">
                <button @click="capture" class="ml-3">Maak een foto!</button>
                <button @click="share">Deel de foto!</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "CreateReceipt",
        data() {
            return {
                mediaStream: null,
                hash: null,
            }
        },
        mounted() {
            navigator.getUserMedia({video: {facingMode: "environment"}})
                .then(mediaStream => {
                    this.mediaStream = mediaStream;
                    this.$refs.video.srcObject = mediaStream;
                    this.$refs.video.play();
                })
                .catch(error => console.error('getUserMedia() error:', error));
        },
        methods: {
            capture() {
                const mediaStreamTrack = this.mediaStream.getVideoTracks()[0];
                const imageCapture = new window.ImageCapture(mediaStreamTrack);
                return imageCapture.takePhoto().then(blob => {
                    const data = new FormData();
                    data.append('receipt', blob, 'receiptImage');
                    console.log(blob);
                    axios.post('/api/receipt', data, {
                        headers: {
                            'Content-Type': `multipart/form-data; boundary=${data._boundary}`,
                        },
                        timeout: 30000,
                    }).then((data) => {
                        console.log(data);
                        if (data.status === 200) {

                            this.hash = data.data.hash;
                        }
                    });
                });
            },
            share() {
                console.log(this.hash);
                if (this.hash !== null) {
                    window.location = `/getreceipt/${this.hash}`;
                }
            }
        },
        destroyed() {
            const tracks = this.mediaStream.getTracks();
            tracks.map(track => track.stop());
        }
    }
</script>
<style scoped>

</style>
