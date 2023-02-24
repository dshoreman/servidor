<template>

    <div v-if="project">

        <h2 is="sui-header" size="huge" :inverted="darkMode" dividing>
            <sui-form-field class="enable-switch">
                <sui-checkbox toggle v-model="project.is_enabled" @change="toggleProject" />
            </sui-form-field>
            <div v-if="renaming">
                <sui-input transparent v-model="newProjectName"
                           @keyup.enter="renameProject()"
                           @keyup.esc="stopRenaming()"/>
                <sui-button-group size="tiny">
                    <sui-button positive icon="check" style="top: -0.3em;"
                        compact attached="left" @click="renameProject()" />
                    <sui-button negative icon="cancel" basic style="top: -0.3em;"
                        compact attached="right" @click="stopRenaming()" />
                </sui-button-group>
            </div>
            <div v-else>
                {{ project.name }}
                <sui-icon name="pencil" link size="tiny" @click="startRenaming()"
                    style="position: relative; bottom: 0.3em; left: 0.3em;" />
            </div>
        </h2>

        <sui-grid>
            <sui-grid-row>
                <sui-grid-column :width="3">
                    <project-tabs :project="project" />
                </sui-grid-column>

                <sui-grid-column :width="13">
                    <div v-for="service in project.services" :key="service.id">
                        <sui-segment inverted color="red"
                            emphasis="secondary" v-if="!service.system_user">
                            <sui-icon name="exclamation triangle" />
                            The system user required by this project does not exist!
                        </sui-segment>

                        <sui-header attached="top" :inverted="darkMode">
                            <ssl-indicator :service="service" style="float: right; margin: 0;" />
                            <sui-label style="float: right; margin: 0 5px 0;"
                                size="tiny" color="violet" title="PHP Version"
                                v-if="service.config && service.config.phpVersion">
                                <sui-icon name="php" /> {{ service.config.phpVersion }}
                            </sui-label>
                            Source Files
                        </sui-header>
                        <sui-segment attached :inverted="darkMode">
                            <sui-grid>
                                <sui-grid-row :columns="2">
                                    <sui-grid-column>
                                        <sui-header size="tiny" :inverted="darkMode">Repository
                                            <sui-header-subheader
                                                v-if="service.config.source.repository">
                                                <sui-icon :name="service.config.source.provider" />
                                                <span>{{ service.config.source.repository }}</span>
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
                                                @click="pullFiles(service)" />
                                            <sui-header-subheader
                                                v-if="service.config.source.branch">
                                                {{ service.config.source.branch }}
                                            </sui-header-subheader>
                                            <sui-header-subheader v-else>
                                                Using default branch
                                            </sui-header-subheader>
                                        </sui-header>
                                    </sui-grid-column>
                                </sui-grid-row>
                            </sui-grid>

                            <sui-header size="tiny" :inverted="darkMode" v-if="service.source_root">
                                <router-link :to="filesLink(service.source_root)" is="sui-button"
                                             content="Browse files" floated="right"
                                             basic primary icon="open folder" />
                                Project Root
                                <sui-header-subheader>
                                    {{ service.source_root }}
                                </sui-header-subheader>
                            </sui-header>
                            <sui-header size="tiny" :inverted="darkMode" v-else>
                                Project Root
                                <sui-header-subheader>
                                    This project doesn't have a project root defined.
                                </sui-header-subheader>
                            </sui-header>
                        </sui-segment>

                        <sui-header attached="top" :inverted="darkMode" v-if="service.system_user">
                            <sui-icon name="check" color="green" style="float: right; margin: 0;" />
                            System User
                        </sui-header>
                        <sui-segment attached :inverted="darkMode" v-if="service.system_user">
                            <sui-grid>
                                <sui-grid-row>
                                    <sui-grid-column :width="8">
                                        <sui-header size="tiny" :inverted="darkMode">Username
                                            <sui-header-subheader>
                                                {{ service.system_user.name }}
                                            </sui-header-subheader>
                                        </sui-header>
                                    </sui-grid-column>
                                    <sui-grid-column :width="4">
                                        <sui-header size="tiny" :inverted="darkMode">User ID
                                            <sui-header-subheader>
                                                {{ service.system_user.uid }}
                                            </sui-header-subheader>
                                        </sui-header>
                                    </sui-grid-column>
                                    <sui-grid-column :width="4">
                                        <sui-header size="tiny" :inverted="darkMode">Group ID
                                            <sui-header-subheader>
                                                {{ service.system_user.gid }}
                                            </sui-header-subheader>
                                        </sui-header>
                                    </sui-grid-column>
                                </sui-grid-row>
                            </sui-grid>

                            <sui-header size="tiny" :inverted="darkMode"
                                v-if="service.system_user.dir">
                                <router-link is="sui-button" content="Browse files" floated="right"
                                            :to="filesLink(service.system_user.dir)"
                                            basic primary icon="open folder" />
                                Home Directory
                                <sui-header-subheader>
                                    {{ service.system_user.dir }}
                                </sui-header-subheader>
                            </sui-header>
                        </sui-segment>

                        <sui-header v-if="hasLogs(service)" attached="top" :inverted="darkMode">
                            Project Logs
                        </sui-header>
                        <project-logs :project="project" :service="service" />
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
import ProjectLogs from '../../components/Projects/Viewer/ProjectLogs';
import ProjectTabs from '../../components/Projects/Viewer/ProjectTabs';
import SslIndicator from '../../components/Projects/Services/SslIndicator';
import { mapActions } from 'vuex';

export default {
    components: {
        ProjectLogs,
        ProjectTabs,
        SslIndicator,
    },
    async mounted() {
        if (!this.$store.getters['projects/all'].length) {
            await this.$store.dispatch('projects/load');
        }
    },
    props: {
        id: { type: Number, default: 0 },
    },
    data() {
        return {
            renaming: false,
            newProjectName: '',
        };
    },
    computed: {
        project() {
            return this.$store.getters['projects/find'](this.id);
        },
    },
    methods: {
        ...mapActions({
            pullFiles: 'projects/pull',
        }),
        filesLink(path) {
            return { name: 'files', params: { path }};
        },
        hasLogs(service) {
            return 0 !== Object.keys(service.logs).length;
        },
        removeProject() {
            /* eslint-disable no-alert */
            confirm('Deletion is permanent! Are you sure?')
                && this.$store.dispatch('projects/remove', this.project.id).then(
                    () => this.$router.push({ name: 'projects' }),
                );
        },
        renameProject() {
            this.$store.dispatch(
                'projects/rename',
                { id: this.project.id, name: this.newProjectName },
            ).finally(() => {
                this.stopRenaming();
            });
        },
        startRenaming() {
            this.renaming = true;
            this.newProjectName = this.project.name;
        },
        stopRenaming() {
            this.renaming = false;
            this.newProjectName = '';
        },
        toggleProject() {
            const enabled = this.project.is_enabled,
                endpoint = enabled ? 'enable' : 'disable';

            this.$store.dispatch(`projects/${endpoint}`, this.project.id).catch(() => {
                this.project.is_enabled = !enabled;
            });
        },
    },
};
</script>
