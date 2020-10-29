<template>

    <sui-grid>
        <sui-grid-column :width="6">
            <step-list :selected="step" :steps="steps" />
        </sui-grid-column>

        <sui-grid-column :width="10">
            <sui-segment>

                <h3 is="sui-header" v-if="step == 'template'">
                    First pick a template to get started
                </h3>
                <sui-card-group :items-per-row="2" v-if="step == 'template'">
                    <sui-card v-for="tpl in templates" :key="tpl.name"
                              :class="!tpl.disabled && 'link ' + tpl.colour"
                              @click="setAppTemplate(tpl)">
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

                <sui-form @submit.prevent="setAppSource()" v-if="step == 'source'">
                    <h3 is="sui-header" content="Where are the project files stored?" />
                    <sui-form-fields inline>
                        <label>Source Provider</label>
                        <sui-form-field v-for="host in sources" :key="host.name">
                            <sui-checkbox radio v-model="source.host"
                                :value="host.name" :label="host.text" />
                        </sui-form-field>
                    </sui-form-fields>
                    <sui-form-field v-if="source.host == 'custom'">
                        <label>Repository URL:</label>
                        <sui-input v-model="source.url" />
                    </sui-form-field>
                    <sui-form-field v-else>
                        <label>Repository:</label>
                        <sui-input placeholder="dshoreman/servidor-test-site"
                            v-model="source.repo" />
                    </sui-form-field>
                    <sui-form-field>
                        <label>Deployment Branch:</label>
                        <sui-input v-model="source.branch" placeholder="master" />
                    </sui-form-field>
                    <step-buttons />
                </sui-form>

                <sui-form @submit.prevent="goto('confirm')" v-if="step == 'domain'">
                    <h3 is="sui-header" content="Set the main entry point for your app" />
                    <sui-form-field>
                        <label>Domain name</label>
                        <sui-input v-model="defaultApp.domain" placeholder="example.com" />
                    </sui-form-field>
                    <step-buttons />
                </sui-form>

                <sui-segment basic aligned="center" v-if="step == 'confirm'">
                    <h3 is="sui-header" content="Let's get this Project started!" />
                    <p>
                        When you continue, the new project will be created with a
                        <strong>{{ defaultApp.template }}</strong> application.
                    </p>
                    <p>
                        Code will be pulled from the <code>{{ source.repo }}</code>
                        repository on <strong>{{ source.host }}</strong> using:<br>
                        <strong>{{ defaultApp.source_repo }}</strong>.
                    </p>
                    <p>
                        If it's enabled, the {{ defaultApp.template }} application
                        will be accessible at <code>{{ defaultApp.domain }}</code>.
                    </p>
                    <p>
                        The project will be configured to track the
                        <code>{{ defaultApp.source_branch }}</code> branch.
                    </p>
                    <sui-form @submit.prevent="createAndEnable(project)">
                        <sui-divider />
                        <sui-grid textAlign="center">
                            <sui-grid-column centered :width="11">
                                <sui-form-field>
                                    <label>Give your project a name:</label>
                                    <sui-input v-model="project.name" />
                                </sui-form-field>
                            </sui-grid-column>
                        </sui-grid>
                        <sui-divider hidden />
                        <sui-button positive size="big">
                            Save and start the application
                        </sui-button>
                        <sui-divider horizontal>Or</sui-divider>
                        <sui-button primary type="button" @click="create(project)">
                            Just save the project
                        </sui-button>
                    </sui-form>
                </sui-segment>

            </sui-segment>
        </sui-grid-column>
    </sui-grid>

</template>

<script>
import StepButtons from '../../components/Projects/StepButtons';
import StepList from '../../components/Projects/StepList';

export default {
    components: {
        StepButtons,
        StepList,
    },
    data() {
        return {
            project: {
                name: '',
                applications: [],
            },
            source: {
                host: 'github',
                branch: '',
                repo: '',
                url: '',
            },
            sources: [{
                name: 'github',
                text: 'GitHub',
                urlFormat: 'https://github.com/%REPO%.git',
            }, {
                name: 'bitbucket',
                text: 'BitBucket',
                urlFormat: 'https://bitbucket.org/%REPO%.git',
            }, {
                name: 'custom',
                text: 'Custom Git URL',
            }],
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
                icon: 'question mark',
                name: 'confirm',
                title: 'Confirmation',
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
    computed: {
        defaultApp: {
            get() {
                return this.project.applications[0];
            },
            set(data) {
                Vue.set(this.project.applications, 0, data);
            },
        },
    },
    methods: {
        goto(step) {
            const currentStep = this.steps.find(s => this.step === s.name),
                nextStep = this.steps.find(s => step === s.name);

            currentStep.completed = true;
            nextStep.disabled = false;
            this.step = nextStep.name;
        },
        setAppTemplate(tpl) {
            this.project.applications.push({
                template: tpl.name,
                domain: '',
            });

            this.goto('source');
        },
        setAppSource() {
            const sourceHost = this.sources.find(s => s.name === this.source.host);
            let { url } = this.source;

            if ('urlFormat' in sourceHost) {
                url = sourceHost.urlFormat.replace('%REPO%', this.source.repo);
            }

            this.defaultApp = {
                ...this.defaultApp,
                source_repo: url,
                source_branch: this.source.branch,
            };

            this.goto('domain');
        },
        create() {
            this.$store.dispatch('projects/create', this.project).then(() => {
                this.$router.push({ name: 'projects.view', params: { id: this.project.name }});
            });
        },
    },
};
</script>
