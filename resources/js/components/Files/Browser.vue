<template>
    <sui-container>
        <h2>{{ path }}</h2>

        <file-list :files="files" />
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
}
</script>
