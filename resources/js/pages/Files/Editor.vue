<template>
    <sui-container id="file-editor">
        <h2>
            <sui-button id="levelup" size="mini" icon="chevron left"
                @click="backToDir" />
            {{ filePath }}
        </h2>

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
    methods: {
        backToDir: function () {
            let path = this.filePath;

            this.$router.push({
                name: 'files',
                params: { path: path.substr(0, path.lastIndexOf('/')) },
            });
        },
    },
}
</script>
