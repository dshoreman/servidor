<template>
    <sui-grid container>
        <sui-grid-column id="file-browser">
            <path-bar :path="currentPath">
                <router-link :to="{ name: 'apps.view', params: { id: site.id }}"
                    is="sui-button" color="teal" icon="globe" floated="right"
                    id="back2site" content="View Application" v-if="site"
                    :data-tooltip="'Open overview for ' + site.name"
                    data-position="left center" />
            </path-bar>

            <file-list :files="files" :path="currentPath" />
        </sui-grid-column>
    </sui-grid>
</template>

<script>
import FileList from '../../components/Files/Browser/FileList';
import PathBar from '../../components/Files/PathBar';
import { mapGetters } from 'vuex';

export default {
    mounted() {
        this.$store.dispatch('sites/load');
        this.$store.dispatch('files/load', { path: this.path });
    },
    beforeRouteUpdate(to, from, next) {
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
        site() {
            return this.findSite(this.currentPath);
        },
    },
};
</script>
