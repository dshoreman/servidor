<template>
    <sui-grid container>
        <sui-grid-column id="file-editor">
            <h2>
                <sui-button id="levelup" icon="chevron left" @click="backToDir" />
                <span class="pathbar">{{ filePath }}</span>
            </h2>

            <sui-segment v-if="file.error == undefined">
                <pre>{{ file.contents }}</pre>
            </sui-segment>

            <sui-segment class="placeholder" v-else>
                <sui-header icon>
                    <sui-icon v-if="file.error.code == 403" name="ban" color="red" />
                    <sui-icon v-else-if="file.error.code == 404" name="search" color="teal" />
                    <sui-icon v-else name="bug" color="orange" />
                    {{ file.error.msg }}
                </sui-header>
            </sui-segment>
        </sui-grid-column>
    </sui-grid>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
    mounted () {
        this.$store.dispatch('files/open', { file: this.filePath });
    },
    props: [
        'filePath',
    ],
    computed: {
        ...mapGetters({
            file: 'files/file',
        }),
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
