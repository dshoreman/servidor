<template>

    <div v-if="projects.length">

        <sui-input placeholder="Search..." icon="search"
            :class="darkMode ? 'inverted search' : 'search'"
            v-model="filter" @keyup.enter="viewSelected" />

        <router-link :to="{ name: 'projects.new' }"
                     is="sui-button" primary icon="add"
                     content="New Project" />

        <sui-table selectable :inverted="darkMode">
            <sui-table-body>
                <sui-table-row v-for="p in filteredProjects" :key="p.id">
                    <sui-table-cell selectable :colspan="getColspan(p)">
                        <router-link :to="{ name: 'projects.view', params: { id: p.id }}">
                            <sui-icon :color="getIcon(p).color" :name="getIcon(p).name" />
                            {{ p.name }}
                        </router-link>
                    </sui-table-cell>
                    <sui-table-cell selectable collapsing v-if="p.services.length">
                        <a :href="'https://' + p.services[0].domain_name">
                            <sui-icon name="external" />
                            {{ p.services[0].domain_name }}
                        </a>
                    </sui-table-cell>
                    <sui-table-cell collapsing v-if="p.services.length">
                        <router-link is="sui-button" basic size="tiny" compact
                            :to="makeBrowseLink(p)" v-if="canBrowse(p)" :inverted="darkMode">
                            <sui-icon name="open folder" /> Browse Source
                        </router-link>
                    </sui-table-cell>
                </sui-table-row>
            </sui-table-body>
        </sui-table>

    </div>

    <sui-segment class="placeholder" :inverted="darkMode" v-else>

        <h3 is="sui-header" text-align="center">
            Seems you don't currently have any projects!
        </h3>

        <router-link :to="{ name: 'projects.new' }" :inverted="darkMode"
            is="sui-button" primary icon="add"
            content="Create a Project" />

    </sui-segment>

</template>

<style scoped>
div.input {
    float: right;
}
td.collapsing.selectable {
    min-width: 15rem;
}
i.external {
    float: right;
    margin-right: 0.75rem;
    padding-left: 0.75rem;
}
</style>

<script>
import templates from './templates.json';

export default {
    mounted() {
        this.$store.dispatch('projects/load');
    },
    props: {
        projects: Array,
    },
    data() {
        return {
            filter: '',
        };
    },
    computed: {
        filteredProjects() {
            return this.projects.filter(
                p => p.name.toLowerCase().includes(this.filter.toLowerCase()),
            );
        },
    },
    methods: {
        getColspan(project) {
            const WIDTH_NO_DOMAIN = 2,
                WIDTH_NO_SERVICE = 3,
                WIDTH_WITH_DOMAIN = 1;

            if (!project.services.length) {
                return WIDTH_NO_SERVICE;
            }

            return project.services[0].domain_name ? WIDTH_WITH_DOMAIN : WIDTH_NO_DOMAIN;
        },
        getIcon(project) {
            let tpl = { icon: 'question mark', colour: 'grey' };

            if (project.services && 0 < project.services.length) {
                tpl = templates.find(
                    t => t.name.toLowerCase() === project.services[0].template.toLowerCase(),
                );
            }

            return { name: tpl.icon, color: tpl.colour };
        },
        canBrowse(project) {
            return project.services.length && 'source_root' in project.services[0];
        },
        makeBrowseLink(project) {
            return {
                name: 'files',
                params: { path: project.services[0].source_root },
            };
        },
        viewSelected() {
            if (0 === this.filteredProjects.length) {
                return;
            }

            const [ project ] = this.filteredProjects;

            this.$router.push({ name: 'projects.view', params: { id: project.id }});
        },
    },
};
</script>
