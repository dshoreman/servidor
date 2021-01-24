<template>
    <sui-grid container>
        <sui-grid-column id="file-browser">
            <path-bar :path="currentPath">
                <router-link :to="{ name: 'projects.view', params: { id: project.id }}"
                    is="sui-button" color="teal" icon="globe" floated="right"
                    :data-tooltip="'Open overview for ' + project.name" data-position="left center"
                    content="View Project" v-if="project" />

                <sui-button class="icon" floated="right" @click.native="toggleNewFile">
                    <i class="icons">
                        <sui-icon name="file outline" />
                        <sui-icon name="add" class="purple corner" />
                    </i>
                </sui-button>

                <div is="sui-button-group" style="float: right">
                    <sui-button class="icon" @click.native="toggleNewDir">
                        <i class="icons" tooltip="New folder">
                            <sui-icon name="folder outline" />
                            <sui-icon name="add" class="purple corner" />
                        </i>
                    </sui-button>
                </div>
            </path-bar>

            <file-list :files="files" :path="currentPath" />

            <sui-modal size="tiny" v-model="promptNewDir">
                <sui-modal-header>Enter folder name</sui-modal-header>
                <sui-modal-content>
                    <sui-input class="fluid" v-model="dirname" @keyup.enter="mkdir"
                        style="border: 1px solid rgba(34, 36, 38, 0.15)" />
                </sui-modal-content>
                <sui-modal-actions>
                    <sui-button positive @click.native="mkdir" content="OK" />
                </sui-modal-actions>
            </sui-modal>

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
        this.$store.dispatch('projects/load');
        this.$store.dispatch('files/load', { path: this.path });
    },
    beforeRouteUpdate(to, from, next) {
        this.$store.dispatch('files/load', { path: to.params.path });
        next();
    },
    data() {
        return {
            dirname: '',
            filename: '',
            promptNewDir: false,
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
            files: 'files/all',
        }),
        createPath() {
            return `${this.currentPath}/${this.filename}`;
        },
        project() {
            return this.$store.getters['projects/findByDocroot'](this.currentPath);
        },
    },
    methods: {
        edit() {
            this.$router.push({
                name: 'files.edit',
                query: { f: this.createPath },
            });
        },
        mkdir() {
            const path = `${this.currentPath}/${this.dirname}`;

            this.$store.dispatch('files/createDir', path).then(() => {
                this.promptNewDir = false;
            });
        },
        toggleNewDir() {
            this.promptNewDir = !this.promptNewDir;
        },
        toggleNewFile() {
            this.promptNewFile = !this.promptNewFile;
        },
    },
};
</script>
