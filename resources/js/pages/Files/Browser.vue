<template>
    <sui-container id="file-browser">
        <h2>
            <router-link :to="{ name: 'apps.edit', params: { id: site.id }}"
                is="sui-button" color="teal" icon="globe" floated="right"
                id="back2site" :content="'Edit ' + site.name" v-if="site" />

            <sui-button id="levelup" icon="level up" @click="upOneLevel"/>

            <sui-breadcrumb class="massive">
                <template v-for="(segment, index) in pathParts">

                    <sui-breadcrumb-section link @click="setPath(segment.path)"
                        v-if="segment.path != currentPath">
                        {{ segment.dirname }}
                    </sui-breadcrumb-section>
                    <sui-breadcrumb-section v-else>
                        {{ segment.dirname }}
                    </sui-breadcrumb-section>

                    <sui-breadcrumb-divider @click="setPath(segment.path)"
                        v-if="index < (pathParts.length - 1)" />

                </template>
            </sui-breadcrumb>
        </h2>

        <file-list :files="files" @set-path="setPathToFile($event)" />
    </sui-container>
</template>

<script>
import { mapGetters, mapMutations } from 'vuex';
import FileList from '../../components/Files/Browser/FileList';

export default {
    mounted () {
        this.$store.dispatch('loadSites');
        this.$store.dispatch('loadFiles', { path: this.path });
    },
    beforeRouteUpdate (to, from, next) {
        this.$store.dispatch('loadFiles', { path: to.params.path });
        next();
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
            'getSiteByDocroot',
            'files',
        ]),
        pathParts: function() {
            let parts = [],
                path = '';

            for (let part of this.currentPath.split('/')) {
                path = path + part + '/';

                parts.push({
                    'path': path.replace(/\/+$/, ''),
                    'dirname': part,
                });
            }

            return parts;
        },
        site: function() {
            return this.getSiteByDocroot(this.currentPath);
        },
    },
    methods: {
        setPath: function (path) {
            path = '' == path ? '/' : path;

            this.$router.push({ name: 'files', params: { path: path } });
        },
        setPathToFile: function (file) {
            let path = this.currentPath == '/' ? '/' + file.filename
                     : this.currentPath + '/' + file.filename

            this.$router.push({ name: 'files', params: { path: path } });
            this.$store.dispatch('loadFiles', { path: path });
        },
        upOneLevel: function () {
            let path = this.currentPath;
            let next = path.substr(0, path.lastIndexOf('/'));
            next = next ? next : '/';

            this.$router.push({ name: 'files', params: { path: next } });
            this.$store.dispatch('loadFiles', { path: next });
        },
    }
}
</script>
