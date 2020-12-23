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
            this.$store.dispatch('projects/load');
        }
    },
    props: {
        id: {
            type: Number,
            default: 0,
        },
    },
    computed: {
        appIcon() {
            const tpl = templates.find(t => t.name === this.project.applications[0].template);

            return {
                name: tpl.icon,
                color: tpl.colour,
            };
        },
        project() {
            return this.$store.getters['projects/find'](this.id);
        },
    },
};
</script>
