<template>

    <sui-grid>
        <sui-grid-column :width="6">
            <step-list :selected="step" :steps="steps" />
        </sui-grid-column>

        <sui-grid-column :width="10">

            <sui-segment v-if="step == 'template'">
                <h3 is="sui-header">First pick a template to get started</h3>
                <template-selector @selected="setAppTemplate" />
            </sui-segment>

            <sui-segment v-else-if="step == 'source'">
                <sui-form @submit.prevent="setAppSource()">
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
            </sui-segment>

            <sui-segment v-else-if="step == 'domain'">
                <h3 is="sui-header">Set the main entry point for your app</h3>
                <domain-form v-model="defaultApp.domain" @next="goto('confirm')" />
            </sui-segment>

            <sui-segment padded aligned="center" v-else-if="step == 'confirm'">
                <h3 is="sui-header">Let's get this Project started!</h3>
                <confirmation-text :app="defaultApp" :source="source" />
                <confirmation-form v-model="project.name"
                    @enabled="createAndEnable"
                    @created="create" />
            </sui-segment>

        </sui-grid-column>
    </sui-grid>

</template>

<script>
import ConfirmationForm from '../../components/Projects/ConfirmationForm';
import ConfirmationText from '../../components/Projects/ConfirmationText';
import DomainForm from '../../components/Projects/Apps/DomainForm';
import StepButtons from '../../components/Projects/StepButtons';
import StepList from '../../components/Projects/StepList';
import TemplateSelector from '../../components/Projects/Apps/TemplateSelector';
import providers from './source-providers.json';
import steps from './steps.json';

export default {
    components: {
        ConfirmationForm,
        ConfirmationText,
        DomainForm,
        StepButtons,
        StepList,
        TemplateSelector,
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
            sources: providers,
            step: 'template',
            steps,
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
        createAndEnable() {
            this.create();
        },
    },
};
</script>
