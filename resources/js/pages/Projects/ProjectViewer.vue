<template>

    <div v-if="project">

        <h2 is="sui-header" size="huge" :inverted="darkMode" dividing>
            <sui-form-field class="enable-switch">
                <sui-checkbox toggle v-model="project.is_enabled" @change="toggleProject" />
            </sui-form-field>
            <div v-if="renaming">
                <sui-input transparent v-model="project.name" @keyup.enter="renameProject()" />
                <sui-button-group size="tiny">
                    <sui-button positive icon="check" style="top: -0.3em;"
                        compact attached="left" @click="renameProject()" />
                    <sui-button negative icon="cancel" basic style="top: -0.3em;"
                        compact attached="right" @click="renaming = false" />
                </sui-button-group>
            </div>
            <div v-else>
                {{ project.name }}
                <sui-icon name="pencil" link size="tiny" @click="renaming = true"
                    style="position: relative; bottom: 0.3em; left: 0.3em;" />
            </div>
        </h2>

        <sui-grid>
            <sui-grid-row>
                <sui-grid-column :width="3">
                    <project-tabs :project="project" />
                </sui-grid-column>

                <sui-grid-column :width="13">
                    <div v-for="app in project.applications" :key="app.id">
                        <sui-segment inverted color="red"
                            emphasis="secondary" v-if="!app.system_user">
                            <sui-icon name="exclamation triangle" />
                            The system user required by this project does not exist!
                        </sui-segment>

                        <sui-header attached="top" :inverted="darkMode">Source Files</sui-header>
                        <sui-segment attached :inverted="darkMode">
                            <sui-grid>
                                <sui-grid-row :columns="2">
                                    <sui-grid-column>
                                        <sui-header size="tiny" :inverted="darkMode">Repository
                                            <sui-header-subheader v-if="app.source_repository">
                                                <sui-icon :name="app.source_provider" />
                                                <span>{{ app.source_repository }}</span>
                                            </sui-header-subheader>
                                            <sui-header-subheader v-else>
                                                No source repository has been set for this project!
                                            </sui-header-subheader>
                                        </sui-header>
                                    </sui-grid-column>
                                    <sui-grid-column>
                                        <sui-header size="tiny" :inverted="darkMode">Tracking Branch
                                            <sui-button basic positive icon="download"
                                                content="Pull Latest Code" floated="right"
                                                @click="pullFiles(app)" />
                                            <sui-header-subheader v-if="app.source_branch">
                                                {{ app.source_branch }}
                                            </sui-header-subheader>
                                            <sui-header-subheader v-else>
                                                Using default branch
                                            </sui-header-subheader>
                                        </sui-header>
                                    </sui-grid-column>
                                </sui-grid-row>
                            </sui-grid>

                            <sui-header size="tiny" :inverted="darkMode" v-if="app.source_root">
                                <router-link :to="filesLink(app.source_root)" is="sui-button"
                                             content="Browse files" floated="right"
                                             basic primary icon="open folder" />
                                Project Root
                                <sui-header-subheader>{{ app.source_root }}</sui-header-subheader>
                            </sui-header>
                            <sui-header size="tiny" :inverted="darkMode" v-else>
                                Project Root
                                <sui-header-subheader>
                                    This project doesn't have a project root defined.
                                </sui-header-subheader>
                            </sui-header>
                        </sui-segment>

                        <sui-header attached="top" :inverted="darkMode" v-if="app.system_user">
                            <sui-icon name="check" color="green" style="float: right; margin: 0;" />
                            System User
                        </sui-header>
                        <sui-segment attached :inverted="darkMode" v-if="app.system_user">
                            <sui-grid>
                                <sui-grid-row>
                                    <sui-grid-column :width="8">
                                        <sui-header size="tiny" :inverted="darkMode">Username
                                            <sui-header-subheader>
                                                {{ app.system_user.name }}
                                            </sui-header-subheader>
                                        </sui-header>
                                    </sui-grid-column>
                                    <sui-grid-column :width="4">
                                        <sui-header size="tiny" :inverted="darkMode">User ID
                                            <sui-header-subheader>
                                                {{ app.system_user.uid }}
                                            </sui-header-subheader>
                                        </sui-header>
                                    </sui-grid-column>
                                    <sui-grid-column :width="4">
                                        <sui-header size="tiny" :inverted="darkMode">Group ID
                                            <sui-header-subheader>
                                                {{ app.system_user.gid }}
                                            </sui-header-subheader>
                                        </sui-header>
                                    </sui-grid-column>
                                </sui-grid-row>
                            </sui-grid>

                            <sui-header size="tiny" :inverted="darkMode" v-if="app.system_user.dir">
                                <router-link :to="filesLink(app.system_user.dir)" is="sui-button"
                                             content="Browse files" floated="right"
                                             basic primary icon="open folder" />
                                Home Directory
                                <sui-header-subheader>
                                    {{ app.system_user.dir }}
                                </sui-header-subheader>
                            </sui-header>
                        </sui-segment>

                        <sui-header v-if="logNames.length" attached="top" :inverted="darkMode">
                            Project Logs
                        </sui-header>
                        <sui-segment attached :inverted="darkMode" v-if="logNames.length">
                            <sui-menu pointing secondary :inverted="darkMode">
                                <a is="sui-menu-item" v-for="(title, key) in app.logs" :key="key"
                                    :active="activeLog === key" :content="title"
                                    @click="viewLog(app.id, key)" />
                            </sui-menu>
                            <pre>{{ logContent }}</pre>
                        </sui-segment>
                    </div>

                    <div v-for="redir in project.redirects" :key="redir.id">
                        <sui-header attached="top" :inverted="darkMode">
                            Domain Redirection
                        </sui-header>
                        <sui-segment attached :inverted="darkMode">
                            <sui-grid>
                                <sui-grid-row>
                                    <sui-grid-column :width="11">
                                        <sui-header size="tiny" :inverted="darkMode">
                                            Target URL
                                            <sui-header-subheader>
                                                {{ redir.target }}
                                            </sui-header-subheader>
                                        </sui-header>
                                    </sui-grid-column>
                                    <sui-grid-column :width="5">
                                        <sui-header size="tiny" :inverted="darkMode">
                                            Redirect Type
                                            <sui-header-subheader v-if="redir.type == 301">
                                                Permanent
                                            </sui-header-subheader>
                                            <sui-header-subheader v-else-if="redir.type == 302">
                                                Temporary
                                            </sui-header-subheader>
                                            <sui-header-subheader v-else>
                                                {{ redir.type }}
                                            </sui-header-subheader>
                                        </sui-header>
                                    </sui-grid-column>
                                </sui-grid-row>
                            </sui-grid>
                        </sui-segment>
                    </div>
                </sui-grid-column>
            </sui-grid-row>
        </sui-grid>

        <sui-divider hidden />

        <sui-button negative icon="trash" content="Delete Project"
            floated="right" type="button" @click="removeProject()" />

    </div>

</template>

<style scoped>
.field.enable-switch {
    float: right;
    margin-top: 0;
}
</style>

<script>
import ProjectTabs from '../../components/Projects/Viewer/ProjectTabs';
import { mapActions } from 'vuex';

export default {
    components: {
        ProjectTabs,
    },
    mounted() {
        if (!this.$store.getters['projects/all'].length) {
            this.$store.dispatch('projects/load').then(() => {
                this.initLog();
            });
        }
    },
    props: {
        id: { type: Number, default: 0 },
    },
    data() {
        return {
            activeLog: '',
            logContent: '',
            renaming: false,
        };
    },
    computed: {
        logNames() {
            if (0 === this.project.applications.length) {
                return [];
            }

            return Object.keys(this.project.applications[0].logs);
        },
        project() {
            return this.$store.getters['projects/find'](this.id);
        },
    },
    methods: {
        ...mapActions({
            pullFiles: 'projects/pull',
        }),
        initLog() {
            const [ app ] = this.project.applications;

            if (this.logNames.length) {
                this.viewLog(app.id, this.logNames[0]);
            } else {
                this.logContent = '';
                this.activeLog = '';
            }
        },
        filesLink(path) {
            return { name: 'files', params: { path }};
        },
        removeProject() {
            /* eslint-disable no-alert */
            confirm('Deletion is permanent! Are you sure?')
                && this.$store.dispatch('projects/remove', this.project.id).then(
                    () => this.$router.push({ name: 'projects' }),
                );
        },
        renameProject() {
            const { id, name } = this.project;

            this.$store.dispatch('projects/rename', { id, name }).finally(() => {
                this.renaming = false;
            });
        },
        toggleProject() {
            const enabled = this.project.is_enabled,
                endpoint = enabled ? 'enable' : 'disable';

            this.$store.dispatch(`projects/${endpoint}`, this.project.id).catch(() => {
                this.project.is_enabled = !enabled;
            });
        },
        viewLog(appId, key) {
            this.logContent = 'Loading...';
            this.activeLog = key;

            axios
                .get(`/api/projects/${this.project.id}/logs/${this.activeLog}.app-${appId}.log`)
                .then(response => {
                    this.logContent = '' === response.data.trim()
                        ? "Log file is empty or doesn't exist."
                        : response.data;
                }).catch(() => {
                    this.logContent = `Failed to load ${this.activeLog} log!`;
                });
        },
    },
};
</script>
