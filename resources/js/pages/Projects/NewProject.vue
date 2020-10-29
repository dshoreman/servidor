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
                <h3 is="sui-header">Where are the project files stored?</h3>
                <source-selector :providers="providers"
                    @selected="setAppSource"
                    @cancelled="cancel" />
            </sui-segment>

            <sui-segment v-else-if="step == 'domain'">
                <h3 is="sui-header">Set the main entry point for your app</h3>
                <domain-form
                    v-model="defaultApp.domain"
                    @next="goto('confirm')"
                    @cancelled="cancel" />
            </sui-segment>

            <sui-segment padded aligned="center" v-else-if="step == 'confirm'">
                <h3 is="sui-header">Let's get this Project started!</h3>
                <confirmation-text :app="defaultApp" :source="extraData" />
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
import SourceSelector from '../../components/Projects/Apps/SourceSelector';
import StepList from '../../components/Projects/StepList';
import TemplateSelector from '../../components/Projects/Apps/TemplateSelector';
import providers from './source-providers.json';
import steps from './steps.json';

export default {
    components: {
        ConfirmationForm,
        ConfirmationText,
        DomainForm,
        StepList,
        SourceSelector,
        TemplateSelector,
    },
    data() {
        return {
            extraData: {
                repository: '',
                provider: '',
            },
            project: {
                name: '',
                applications: [],
            },
            providers,
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
        cancel() {
            this.extraData = {
                repository: '',
                provider: '',
            };

            this.project = {
                name: '',
                applications: [ {} ],
            };

            this.steps.forEach(s => {
                if ('template' !== s.name) {
                    s.completed = false;
                    s.disabled = true;
                }
            });

            this.step = 'template';
        },
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
        setAppSource(source) {
            this.defaultApp = {
                ...this.defaultApp,
                repository: source.repository,
                branch: source.branch,
            };

            this.extraData = {
                provider: source.provider,
                repository: source.repoName,
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
