<template>
    <sui-grid container>
        <sui-grid-column id="file-browser">
            <h2>
                <router-link :to="{ name: 'apps.view', params: { id: site.id }}"
                    is="sui-button" color="teal" icon="globe" floated="right"
                    id="back2site" content="View Application" v-if="site"
                    :data-tooltip="'Open overview for ' + site.name"
                    data-position="left center" />

                <sui-button id="levelup" icon="level up" @click="upOneLevel" />

                <path-bar :path="currentPath" @cd="setPath($event)" />
            </h2>

            <file-list :files="files" @cd="setPath($event)" />
        </sui-grid-column>
    </sui-grid>
</template>

<script>
import { mapGetters, mapMutations } from 'vuex';
import FileList from '../../components/Files/Browser/FileList';
import PathBar from '../../components/Files/PathBar';

export default {
    mounted () {
        this.$store.dispatch('sites/load');
        this.$store.dispatch('files/load', { path: this.path });
    },
    beforeRouteUpdate (to, from, next) {
        this.$store.dispatch('files/load', { path: to.params.path });
        next();
    },
    props: [
        'path',
    ],
    components: {
        FileList,
        PathBar,
    },
    computed: {
        ...mapGetters({
            currentPath: 'files/currentPath',
            findSite: 'sites/findByDocroot',
            files: 'files/all',
        }),
        site: function() {
            return this.findSite(this.currentPath);
        },
    },
    methods: {
        setPath: function (target) {
            if (typeof target == 'string') {
                target = '' == target ? '/' : target;
            } else {
                target = this.currentPath == '/' ? '/' + target.filename
                     : this.currentPath + '/' + target.filename
            }

            this.$router.push({ name: 'files', params: { path: target } });
        },
        upOneLevel: function () {
            let path = this.currentPath;
            let next = path.substr(0, path.lastIndexOf('/'));
            next = next ? next : '/';

            this.$router.push({ name: 'files', params: { path: next } });
            this.$store.dispatch('files/load', { path: next });
        },
    }
}
</script>
