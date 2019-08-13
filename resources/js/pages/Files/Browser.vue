<template>
    <sui-container id="file-browser">
        <h2>
            <sui-button id="levelup" size="mini" icon="level up" @click="upOneLevel"/>
            {{ currentPath }}
        </h2>

        <file-list :files="files" @set-path="setPath($event)" />
    </sui-container>
</template>

<script>
import { mapGetters, mapMutations } from 'vuex';
import FileList from '../../components/Files/Browser/FileList';

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
            'currentPath',
            'files',
        ]),
    },
    methods: {
        setPath: function (file) {
            this.$store.dispatch('loadFiles', {
                path: this.currentPath == '/' ? '/' + file.filename
                    : this.currentPath + '/' + file.filename
            });
        },
        upOneLevel: function () {
            let path = this.currentPath;
            let next = path.substr(0, path.lastIndexOf('/'))

            this.$store.dispatch('loadFiles', {
                path: next ? next : '/'
            });
        },
    }
}
</script>
