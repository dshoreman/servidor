<template>
    <sui-grid container>
        <sui-grid-column id="file-browser">
            <path-bar :path="currentPath">
                <router-link :to="{ name: 'apps.view', params: { id: site.id }}"
                    is="sui-button" color="teal" icon="globe" floated="right"
                    id="back2site" content="View Application" v-if="site"
                    :data-tooltip="'Open overview for ' + site.name"
                    data-position="left center" />

                <sui-button class="icon" floated="right" @click.native="toggleNewFile">
                    <i class="icons">
                        <sui-icon name="file outline" />
                        <sui-icon name="add" class="purple corner" />
                    </i>
                </sui-button>
            </path-bar>

            <file-list :files="files" :path="currentPath" />

            <sui-modal size="tiny" v-model="promptNewFile">
                <sui-modal-header>Enter filename</sui-modal-header>
                <sui-modal-content>
                    <sui-input class="fluid" v-model="filename" @keyup.enter="edit"
                        style="border: 1px solid rgba(34, 36, 38, 0.15)" />
                </sui-modal-content>
                <sui-modal-actions>
                    <sui-button positive @click.native="edit" content="OK" />
                </sui-modal-actions>
            </sui-modal>
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
    data() {
        return {
            filename: '',
            promptNewFile: false,
        };
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
        createPath() {
            return `${this.currentPath}/${this.filename}`;
        },
        site() {
            return this.findSite(this.currentPath);
        },
    },
    methods: {
        edit() {
            this.$router.push({
                name: 'files.edit',
                query: { f: this.createPath },
            });
        },
        toggleNewFile() {
            this.promptNewFile = !this.promptNewFile;
        },
    },
};
</script>
