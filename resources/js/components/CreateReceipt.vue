<template>
  <div class="flex-container">
    <div class="card">
      <div class="camera-modal">
        <video ref="video" class="camera-stream"/>
      </div>
      <div class="bottom-part custom-flex">
        <button @click="capture" class="ml-3" :class="{ 'active': blueActive }">Scan</button>
        <button @click="share">Share it!</button>
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
        blueActive: false,
      }
    },
    mounted() {
      navigator.mediaDevices.getUserMedia({video: {facingMode: "environment"}})
               .then(mediaStream => {
                 this.mediaStream = mediaStream;
                 this.$refs.video.srcObject = mediaStream;
                 this.$refs.video.play()
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
              this.blueActive = true;
            }
          });
        });
      },
      share() {
        console.log(this.hash);
        if (this.hash !== null) {
          window.location = `/getreceipt/${this.hash}`
        }
      }
    },
    destroyed() {
      const tracks = this.mediaStream.getTracks();
      tracks.map(track => track.stop());
    }
  }
</script>
<style scss scoped>
  .flex-container .card {
    border-radius: 12px;
    margin: 0;
  }

  .flex-container {
    margin: 0;
    height: 100vh;
    overflow: hidden;
    align-items: center;
    justify-content: center;
  }

  .flex-container .card[data-v-d200f97e] {
    padding: 0;
    margin: 0;
    border-radius: 12px;
  }

  .camera-modal {
    line-height: 0;
    margin: 20px;
    overflow: hidden;
    border-radius: 12px;
  }

  button:hover {
    background-color: #157efb;
    transition: 0.5s;
  }

  button.active, button.active:hover {
    background-color: rgba(21, 126, 251, 0.5) !important;
    transition: 0.5s;
    pointer-events: none;

  }

  ::-webkit-scrollbar-button {
    display: block;
    height: 13px;
    border-radius: 0px;
    background-color: #AAA;
  }

  ::-webkit-scrollbar-button:hover {
    background-color: #AAA;
  }

  ::-webkit-scrollbar-thumb {
    background-color: #157efb;
    border-radius: 10px;
  }

  ::-webkit-scrollbar-thumb:hover {
    background-color: #157efb;
    border-radius: 10px;
  }

  ::-webkit-scrollbar-track {
    background-color: #fafafa;
  }

  ::-webkit-scrollbar-track:hover {
    background-color: #CCC;
  }

  ::-webkit-scrollbar {
    width: 4px;
  }


</style>
