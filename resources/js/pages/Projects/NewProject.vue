<template>

    <sui-grid>
        <sui-grid-column :width="6">
            <sui-step-group vertical fluid>
                <sui-step icon="edit" active title="Name your Project" />
            </sui-step-group>
        </sui-grid-column>

        <sui-grid-column :width="10">
            <sui-segment>

                <sui-form @submit.prevent="create(project)" v-if="step == 'name'">
                    <sui-form-field>
                        <sui-header size="small">
                            <label>Name your Project</label>
                        </sui-header>
                        <sui-input v-model="project.name" />
                    </sui-form-field>
                    <sui-button primary content="Create project" />
                </sui-form>

            </sui-segment>
        </sui-grid-column>
    </sui-grid>

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
                this.$router.push({ name: 'projects.view', params: { id: this.project.name }});
            });
        },
    },
};
</script>
