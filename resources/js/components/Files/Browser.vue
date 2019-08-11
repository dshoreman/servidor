<template>
    <sui-container>
        <h2>{{ path }}</h2>

        <file-list :files="files" @set-path="setPath($event)" />
    </sui-container>
</template>

<script>
import { mapGetters, mapMutations } from 'vuex';
import FileList from './Browser/FileList';

export default {
    mounted () {
        this.$store.dispatch('loadFiles', { path: this.path });
    },
    props: [
        'path',
    ],
    components: {
        FileList,
    },
    computed: {
        ...mapGetters([
            'files',
        ]),
    },
    methods: {
        setPath: function (file) {
            this.$store.dispatch('loadFiles', {
                path: this.path + '/' + file.filename,
            });
        },
    }
}
</script>
