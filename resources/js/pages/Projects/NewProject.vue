<template>

    <sui-grid>
        <sui-grid-column :width="6">
            <sui-step-group vertical fluid>
                <sui-step v-for="s in steps" :key="s.title" :icon="s.icon"
                          :active="step == s.name" :completed="s.completed"
                          :title="s.title" :disabled="s.disabled" />
            </sui-step-group>
        </sui-grid-column>

        <sui-grid-column :width="10">
            <sui-segment>

                <h3 is="sui-header" v-if="step == 'template'">
                    First pick a template to get started
                </h3>
                <sui-card-group :items-per-row="2" v-if="step == 'template'">
                    <sui-card v-for="tpl in templates" :key="tpl.name"
                              :class="!tpl.disabled && 'link ' + tpl.colour"
                              @click="goto('source')">
                        <sui-card-content>
                            <h3 is="sui-header">
                                <sui-icon :name="tpl.icon" :color="tpl.colour" size="big" />
                                <sui-header-content>
                                    {{ tpl.name }}
                                    <sui-header-subheader>{{ tpl.text }}</sui-header-subheader>
                                </sui-header-content>
                            </h3>
                        </sui-card-content>
                    </sui-card>
                </sui-card-group>

                <sui-form @submit.prevent="goto('domain')" v-if="step == 'source'">
                    <sui-form-fields inline>
                        <label>Source Provider</label>
                        <sui-form-field>
                            <sui-checkbox radio label="GitHub" />
                        </sui-form-field>
                        <sui-form-field>
                            <sui-checkbox radio label="Bitbucket" />
                        </sui-form-field>
                        <sui-form-field>
                            <sui-checkbox radio label="Custom Git URL" />
                        </sui-form-field>
                    </sui-form-fields>
                    <sui-form-field v-if="provider == 'custom'">
                        <label>Repository URL:</label>
                        <sui-input />
                    </sui-form-field>
                    <sui-form-field v-else>
                        <label>Repository:</label>
                        <sui-input placeholder="dshoreman/servidor-test-site" />
                    </sui-form-field>
                    <sui-form-field>
                        <label>Deployment Branch:</label>
                        <sui-input placeholder="master" />
                    </sui-form-field>
                    <sui-button primary content="Load branches" />
                </sui-form>

                <sui-form @submit.prevent="goto('name')" v-if="step == 'domain'">
                    <sui-form-field>
                        <sui-header size="small">
                            <label>Enter the primary domain name for your application</label>
                        </sui-header>
                        <sui-input />
                    </sui-form-field>
                    <sui-button primary content="Save domain" />
                </sui-form>

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
            provider: '',
            step: 'template',
            steps: [{
                icon: 'rocket',
                name: 'template',
                title: 'Project Template',
            }, {
                disabled: true,
                icon: 'code',
                name: 'source',
                title: 'Source Location',
            }, {
                disabled: true,
                icon: 'globe',
                name: 'domain',
                title: 'Primary Domain',
            }, {
                disabled: true,
                icon: 'edit',
                name: 'name',
                title: 'Name your Project',
            }],
            templates: [{
                icon: 'archive',
                colour: 'brown',
                disabled: true,
                name: 'Archive',
                text: 'Redirect all requests on a domain to an archived copy on Wayback Machine.',
            }, {
                icon: 'question mark',
                colour: 'grey',
                name: 'Clean Slate',
                text: "Don't setup an application component for now, just create an empty project.",
            }, {
                icon: 'docker',
                colour: 'blue',
                disabled: true,
                name: 'Docker',
                text: 'Install and configure a docker container to run automatically on system boot.',
            }, {
                icon: 'html5',
                colour: 'orange',
                name: 'HTML',
                text: 'Configure nginx to serve static content such as HTML, CSS and Javascript.',
            }, {
                icon: 'php',
                colour: 'violet',
                name: 'PHP',
                text: 'Like HTML projects, but with added support for dynamic PHP content.',
            }, {
                icon: 'laravel',
                colour: 'red',
                name: 'Laravel',
                text: 'Modified PHP project with hooks for artisan migrations and composer/npm.',
            }],
        };
    },
    methods: {
        goto(step) {
            const currentStep = this.steps.find(s => this.step === s.name),
                nextStep = this.steps.find(s => step === s.name);

            currentStep.completed = true;
            nextStep.disabled = false;
            this.step = nextStep.name;
        },
        create() {
            this.$store.dispatch('projects/create', this.project).then(() => {
                this.$router.push({ name: 'projects.view', params: { id: this.project.name }});
            });
        },
    },
};
</script>
