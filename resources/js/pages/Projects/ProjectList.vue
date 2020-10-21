<template>

    <div v-if="projects.length">

        <router-link :to="{ name: 'projects.new' }"
                     is="sui-button" primary icon="add"
                     content="New Project" />

        <sui-table selectable>
            <sui-table-row  v-for="p in projects" :key="p.name">
                <sui-table-cell>
                    <sui-icon color="violet" name="project" />
                    <router-link :to="{ name: 'projects.view', params: { id: p.name }}">
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
export default {
    props: {
        projects: Array,
    },
};
</script>
