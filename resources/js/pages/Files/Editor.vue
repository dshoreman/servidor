<template>
    <sui-container id="file-editor">
        <h2>
            <sui-button id="levelup" size="mini" icon="chevron left"
                @click="backToDir" />
            {{ filePath }}
        </h2>

        <pre v-if="file.error == undefined">{{ file.contents }}</pre>

        <sui-segment class="placeholder" v-else>
            <sui-header icon>
                <sui-icon v-if="file.error.code == 403" name="ban" color="red" />
                <sui-icon v-else-if="file.error.code == 404" name="search" color="teal" />
                <sui-icon v-else name="bug" color="orange" />
                {{ file.error.msg }}
            </sui-header>
        </sui-segment>

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
