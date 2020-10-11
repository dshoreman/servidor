<template>
    <sui-grid container>
        <sui-grid-column id="file-editor">
            <path-bar :path="filePath" upIcon="chevron left">
                <sui-button positive floated="right" content="Save" @click="save(filePath)" />
            </path-bar>

            <sui-menu attached="top" v-if="file.error == undefined" :inverted="darkMode">
                <sui-dropdown item class="icon" icon="paint brush"
                              labeled floating search button
                              v-model="theme" :options="themes" />

                <sui-dropdown item class="icon" icon="code"
                              labeled floating search button
                              v-model="mode" :options="mappedModes" />

                <sui-menu-menu position="right">
                    <sui-menu-item right>
                        <sui-checkbox label="Line Wrapping" toggle v-model="wrap"/>
                    </sui-menu-item>
                </sui-menu-menu>
            </sui-menu>

            <sui-segment v-if="creating || file.error == undefined" class="code"
                attached="bottom" :inverted="darkMode" :loading="loading">
                <codemirror v-model="file.contents" :options="options" v-if="!loading" />
            </sui-segment>

            <sui-segment class="placeholder" :inverted="darkMode" v-else>
                <sui-header icon>
                    <sui-icon v-if="file.error.code == 403" name="ban" color="red" />
                    <sui-icon v-else-if="file.error.code == 404" name="search" color="teal" />
                    <sui-icon v-else-if="file.error.code == 415"
                        name="help circle" color="violet" />
                    <sui-icon v-else name="bug" color="orange" />
                    {{ file.error.msg }}
                </sui-header>
            </sui-segment>
        </sui-grid-column>
    </sui-grid>
</template>

<script>
import 'codemirror/lib/codemirror.css';
import 'codemirror/theme/dracula.css';

import 'codemirror/addon/scroll/simplescrollbars.css';
import 'codemirror/addon/scroll/simplescrollbars.js';
import 'codemirror/addon/selection/active-line.js';

import PathBar from '../../components/Files/PathBar';
import { codemirror } from 'vue-codemirror';
import { mapGetters } from 'vuex';

const NOT_FOUND = 404;

export default {
    components: {
        codemirror,
        PathBar,
    },
    async mounted() {
        this.loading = true;
        await this.$store.dispatch('files/open', { file: this.filePath })
            .finally(() => {
                this.loading = false;
            });
        this.$store.dispatch('editor/setMode', this.filePath);
    },
    props: [
        'filePath',
    ],
    data: () => ({
        loading: false,
    }),
    computed: {
        ...mapGetters({
            file: 'files/file',
            options: 'editor/options',
            themes: 'editor/themes',
            modes: 'editor/modes',
        }),
        mappedModes() {
            return this.modes.map(o => ({ text: o.name, value: o.mime }));
        },
        theme: {
            get() {
                return this.$store.state.editor.options.theme;
            },
            set(value) {
                this.$store.dispatch('editor/setTheme', value);
            },
        },
        mode: {
            get() {
                return this.$store.state.editor.selectedMode;
            },
            set(value) {
                this.$store.dispatch('editor/setMode', value);
            },
        },
        wrap: {
            get() {
                return this.$store.state.editor.options.lineWrapping;
            },
            set(value) {
                this.$store.dispatch('editor/setLineWrapping', value);
            },
        },
        creating() {
            return 'undefined' !== typeof this.file.error && NOT_FOUND === this.file.error.code;
        },
    },
    methods: {
        save(path) {
            const method = this.creating ? 'create' : 'save';

            this.loading = true;
            this.$store.dispatch(`files/${method}`, path).finally(() => {
                this.loading = false;
            });
        },
    },
};
</script>
