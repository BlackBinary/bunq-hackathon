<template>
    <div>
        <img :src="hashImgUrl" alt="Receipt">

        {{ hashData }}
    </div>

</template>

<script>
    export default {
        name: "GetReceipt",
        props: ['hash'],
        data() {
            return {
                bucketUrl: 'https://bunq-hackathon.ams3.digitaloceanspaces.com/'
            }
        }
        ,
        mounted() {

            console.log(this.hash);
        }
        ,
        computed: {
            hashImgUrl() {
                return `${this.bucketUrl}${this.hash}.png`;
            },
            hashData() {
                return axios.get(`${this.bucketUrl}${this.hash}.json`).then((data) => {
                    console.log(data);
                    // if (data.status === 200) {
                    //
                    //     this.hash = data.data.hash;
                    // }
                });

            }
        }
    }
</script>

<style scoped>

</style>
