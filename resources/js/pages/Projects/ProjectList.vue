<template>

    <div v-if="projects.length">

        <router-link :to="{ name: 'projects.new' }"
                     is="sui-button" primary icon="add"
                     content="New Project" />

        <sui-table>
            <sui-table-body>
                <sui-table-row v-for="p in projects" :key="p.id">
                    <sui-table-cell selectable>
                        <router-link :to="{ name: 'projects.view', params: { id: p.id }}">
                            <sui-icon :color="getIcon(p).color" :name="getIcon(p).name" />
                            {{ p.name }}
                        </router-link>
                    </sui-table-cell>
                    <sui-table-cell collapsing>
                        <router-link is="sui-button" basic size="tiny" compact
                            :to="makeBrowseLink(p)" v-if="canBrowse(p)">
                            <sui-icon name="open folder" /> Browse Source
                        </router-link>
                    </sui-table-cell>
                </sui-table-row>
            </sui-table-body>
        </sui-table>

    </div>

    <sui-segment class="placeholder" v-else>

        <h3 is="sui-header" text-align="center">
            Seems you don't currently have any projects!
        </h3>

        <router-link :to="{ name: 'projects.new' }"
            is="sui-button" primary icon="add"
            content="Create a Project" />

    </sui-segment>

</template>

<script>
import templates from './templates.json';

export default {
    mounted() {
        this.$store.dispatch('projects/load');
    },
    props: {
        projects: Array,
    },
    methods: {
        getIcon(project) {
            let tpl = { icon: 'question mark', colour: 'grey' };

            if (project.applications && 0 < project.applications.length) {
                tpl = templates.find(
                    t => t.name.toLowerCase() === project.applications[0].template.toLowerCase(),
                );
            }

            return { name: tpl.icon, color: tpl.colour };
        },
        canBrowse(project) {
            return project.applications.length && 'source_root' in project.applications[0];
        },
        makeBrowseLink(project) {
            return {
                name: 'files',
                params: { path: project.applications[0].source_root },
            };
        },
    },
};
</script>
