<template>

    <sui-segment>
        <sui-form @submit.prevent="create(project)">

            <sui-form-field>
                <sui-header size="small">
                    <label>Name your Project</label>
                </sui-header>
                <sui-input v-model="project.name" />
            </sui-form-field>

            <sui-button primary content="Create project" />

        </sui-form>
    </sui-segment>

</template>

<script>
export default {
    data() {
        return {
            project: {
                name: '',
            },
        };
    },
    methods: {
        create() {
            this.$store.dispatch('projects/create', this.project).then(() => {
                this.$router.push({ name: 'projects' });
            });
        },
    },
};
</script>