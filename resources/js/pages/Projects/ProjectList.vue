<template>

    <div v-if="projects.length">

        <router-link :to="{ name: 'projects.new' }"
                     is="sui-button" primary icon="add"
                     content="New Project" />

        <sui-table selectable>
            <sui-table-row  v-for="p in projects" :key="p.id">
                <sui-table-cell>
                    <sui-icon :color="getIcon(p).color" :name="getIcon(p).name" />
                    <router-link :to="{ name: 'projects.view', params: { id: p.id }}">
                        {{ p.name }}
                    </router-link>
                </sui-table-cell>
            </sui-table-row>
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
                tpl = templates.find(t => t.name === project.applications[0].template);
            }

            return { name: tpl.icon, color: tpl.colour };
        },
    },
};
</script>
