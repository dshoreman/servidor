<template>
    <sui-grid container>
        <sui-grid-column id="file-editor">
            <h2>
                <sui-button id="levelup" icon="chevron left" @click="backToDir" />
                <span class="pathbar">{{ filePath }}</span>
            </h2>

            <sui-segment v-if="file.error == undefined" :loading="loading">
                <sui-dropdown class="icon" icon="paint brush"
                              labeled floating button search
                              v-model="theme"
                              :options="themes">
                </sui-dropdown>

                <sui-dropdown class="icon" icon="code"
                              labeled floating button search
                              v-model="mode"
                              :options="mappedModes">
                </sui-dropdown>
                <sui-checkbox label="Line Wrapping" toggle v-model="wrap"/>
            </sui-segment>

            <sui-segment v-if="file.error == undefined" :loading="loading">
                <codemirror v-model="file.contents" :options="options"></codemirror>
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
import { codemirror } from 'vue-codemirror'
import 'codemirror/lib/codemirror.css'
import 'codemirror/theme/dracula.css'

export default {
    components: {
        codemirror,
    },
    async mounted () {
        this.loading = true;
        await this.$store.dispatch('files/open', { file: this.filePath })
            .finally(() => this.loading = false );
        this.$store.dispatch('editor/setMode', this.filePath);
    },
    props: [
        'filePath',
    ],
    data: () => {
        return {
            loading: false,
        };
    },
    computed: {
        ...mapGetters({
            file: 'files/file',
            options: 'editor/options',
            themes: 'editor/themes',
            modes: 'editor/modes',
        }),
        mappedModes() {
          return this.modes.map(o => {
            return { text: o.name, value: o.mime };
          });
        },
        theme: {
            get () {
                return this.$store.state.editor.options.theme
            },
            set (value) {
                this.$store.dispatch('editor/setTheme', value)
            }
        },
        mode: {
            get () {
                return this.$store.state.editor.selectedMode
            },
            set (value) {
                this.$store.dispatch('editor/setMode', value)
            }
        },
        wrap: {
            get () {
                return this.$store.state.editor.options.lineWrapping
            },
            set (value) {
                this.$store.dispatch('editor/setLineWrapping', value)
            }
        }
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
