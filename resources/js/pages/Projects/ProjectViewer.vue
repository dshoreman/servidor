<template>

    <div v-if="project">

        <h2 is="sui-header" size="huge" :inverted="darkMode" dividing>
            {{ project.name }}
        </h2>

        <sui-grid>
            <sui-grid-row>
                <sui-grid-column :width="3">
                    <sui-list divided relaxed style="margin-top: 10px;">
                        <sui-list-item v-for="app in project.applications" :key="app.id">
                            <sui-list-icon :name="appIcon.name"
                                size="large" :color="appIcon.color" />
                            <sui-list-content>
                                <a is="sui-list-header">{{ app.domain_name }}</a>
                                <a is="sui-list-description">{{ app.template }}</a>
                            </sui-list-content>
                        </sui-list-item>
                    </sui-list>
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
                                        <sui-header size="tiny" :inverted="darkMode">
                                            Repository
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
                                        <sui-header size="tiny" :inverted="darkMode">
                                            Tracking Branch
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

                            <sui-header size="tiny" :inverted="darkMode" v-if="app.project_root">
                                <router-link :to="{ name: 'files', params: { path: app.project_root } }"
                                             content="Browse files" is="sui-button" floated="right"
                                             basic primary icon="open folder" />
                                Project Root
                                <sui-header-subheader>{{ app.project_root }}</sui-header-subheader>
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
                                        <sui-header size="tiny" :inverted="darkMode">
                                            Username
                                            <sui-header-subheader>
                                                {{ app.system_user.name }}
                                            </sui-header-subheader>
                                        </sui-header>
                                    </sui-grid-column>
                                    <sui-grid-column :width="4">
                                        <sui-header size="tiny" :inverted="darkMode">
                                            User ID
                                            <sui-header-subheader>
                                                {{ app.system_user.uid }}
                                            </sui-header-subheader>
                                        </sui-header>
                                    </sui-grid-column>
                                    <sui-grid-column :width="4">
                                        <sui-header size="tiny" :inverted="darkMode">
                                            Group ID
                                            <sui-header-subheader>
                                                {{ app.system_user.gid }}
                                            </sui-header-subheader>
                                        </sui-header>
                                    </sui-grid-column>
                                </sui-grid-row>
                            </sui-grid>

                            <sui-header size="tiny" :inverted="darkMode" v-if="app.system_user.dir">
                                <router-link :to="{ name: 'files', params: { path: app.system_user.dir }}"
                                             content="Browse files" is="sui-button" floated="right"
                                             basic primary icon="open folder" />
                                Home Directory
                                <sui-header-subheader>{{ app.system_user.dir }}</sui-header-subheader>
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
                </sui-grid-column>
            </sui-grid-row>
        </sui-grid>

    </div>

</template>

<script>
import templates from './templates.json';

export default {
    mounted() {
        if (!this.$store.getters['projects/all'].length) {
            this.$store.dispatch('projects/load').then(() => {
                this.initLog();
            });
        }
    },
    props: {
        id: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            activeLog: '',
            logContent: '',
        };
    },
    computed: {
        appIcon() {
            const tpl = templates.find(t => t.name === this.project.applications[0].template);

            return {
                name: tpl.icon,
                color: tpl.colour,
            };
        },
        logNames() {
            return Object.keys(this.project.applications[0].logs);
        },
        project() {
            return this.$store.getters['projects/find'](this.id);
        },
    },
    methods: {
        initLog() {
            const [ app ] = this.project.applications;

            if (this.logNames.length) {
                this.viewLog(app.id, this.logNames[0]);
            } else {
                this.logContent = '';
                this.activeLog = '';
            }
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
