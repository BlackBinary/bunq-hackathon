<template>
    <div>
        <img :src="hashImgUrl" alt="Receipt">
        <template v-if="data.responses[0]">
            {{ data.responses[0].textAnnotations[0].description }}
        </template>
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
                        console.log(this.data);
                    }
                });
            }
        },
        computed: {
            hashImgUrl() {
                return `${this.bucketUrl}${this.hash}.png`;
            },
        }
    }
</script>

<style scoped>

</style>
