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
                <div class="col-md-6 left">
                  <h2>Send a payment to:</h2>
                  <div class="avatar">
                    <img src="../../img/_DSC0112.3_cut.jpg"
                         class="rounded-circle" alt="Avatar">
                    <p class="first-name">Joost</p>
                  </div>
                  <h2 class="mt-3">Of course you can pay me more!</h2>
                  <div class="list">
                    <div
                      class="item"
                      :class="{
                                                'paid': names[index] !== 'Not yet paid...',
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
                <div class="col-md-6 right">
                  <img :src="hashImgUrl" class="w-100" alt="Receipt">
                </div>
              </div>
            </div>
          </div>
          <div class="bottom-part">
            <button :class="{ 'enough': amount > 0 }" :disabled="!amount > 0">Pay securely with iDEAL
            </button>
            <p>Pay with <a href="https://www.klarna.com/sofort/">Sofort</a> instead</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
  div#app {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    overflow: hidden;
  }

  h1.transaction-amount {
    padding: 10px 0;
  }

  .flex-container .card .list {
    padding-right: 5px;
  }

  @media only screen and  (max-width: 767.98px) {
    .title {
      height: unset;
    }
  }

  ::-webkit-scrollbar-button {
    display: block;
    height: 13px;
    border-radius: 0px;
    background-color: #fafafa;
  }

  ::-webkit-scrollbar-button:hover {
    background-color: #fafafa;
  }

  ::-webkit-scrollbar-thumb {
    background-color: #157efb;
    border-radius: 2px;
  }

  ::-webkit-scrollbar-thumb:hover {
    background-color: #157efb;
    border-radius: 2px;
  }

  ::-webkit-scrollbar-track {
    background-color: #fafafa;
  }

  ::-webkit-scrollbar-track:hover {
    background-color: #fafafa;
  }

  ::-webkit-scrollbar {
    width: 5px;
  }


</style>
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
          'Not yet paid...',
          'Isabelle',
          'Floris',
          'Olivier',
          'Feline',
          'Esther',
          'Not yet paid...',
          'Floris',
          'Not yet paid...',
          'Isabelle',
          'Feline',
          'Not yet paid...',
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
        if (status !== 'Not yet paid...') {
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
      tracks.map(track => track.stop());
    }

  }

</script>


