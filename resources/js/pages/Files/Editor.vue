<template>
    <sui-container id="file-editor">
        <h2>{{ filePath }}</h2>

        <pre>{{ file.contents }}</pre>
    </sui-container>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
    mounted () {
        this.$store.dispatch('openFile', { file: this.filePath });
    },
    props: [
        'filePath',
    ],
    computed: {
        ...mapGetters([
            'file',
        ]),
    },
}
</script>
