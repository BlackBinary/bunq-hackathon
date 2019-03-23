<template>

</template>

<script>
    export default {
        name: "GetReceipt",
        props: ['hash'],
        mounted() {
            axios.get(`/api/receipt/${this.hash}`).then((data) => {
                console.log(data);
                // if (data.status === 200) {
                //
                //     this.hash = data.data.hash;
                // }
            });
            console.log(this.hash);
        }
    }
</script>

<style scoped>

</style>
