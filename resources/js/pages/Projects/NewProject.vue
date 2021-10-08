<template>
    <sui-grid>
        <sui-grid-column :width="16" v-if="error">
            <sui-message negative :content="error" header="Couldn't create the project!" />
        </sui-grid-column>
        <sui-grid-column :width="6">
            <step-list :selected="step" :steps="steps" />
        </sui-grid-column>

        <sui-grid-column :width="10">

            <sui-segment v-if="step == 'template'">
                <h3 is="sui-header">First pick a template to get started</h3>
                <sui-message negative v-if="'template' in errors"
                    :content="errors['applications.0.template'][0]" />
                <template-selector :templates="templates"
                    @selected="setAppTemplate" />
            </sui-segment>

            <sui-segment v-else-if="step == 'source'">
                <h3 is="sui-header">Where are the project files stored?</h3>
                <source-selector :errors="errors" :providers="providers"
                    @selected="setAppSource" @cancel="cancel" />
            </sui-segment>

            <sui-segment v-else-if="step == 'domain'">
                <h3 is="sui-header">Set the main entry point for your app</h3>
                <domain-form :errors="errors" v-model="defaultApp.domain"
                    @next="nextStep('domain')" @cancel="cancel" />
            </sui-segment>

            <sui-segment v-else-if="step == 'redirect'">
                <h3 is="sui-header">Configure the preferred target archive on WayBack Machine</h3>
                <redirect-form :errors="errors" :domain="defaultApp.domain"
                    @next="setRedirect" @cancel="cancel" />
            </sui-segment>

            <sui-segment padded aligned="center" v-else-if="step == 'confirm'">
                <h3 is="sui-header">Let's get this Project started!</h3>
                <confirmation-text :app="defaultApp" :source="extraData" />
                <confirmation-form :errors="errors" v-model="project.name"
                                   :template="defaultApp.template"
                                   @created="create" />
            </sui-segment>

            <progress-modal />

        </sui-grid-column>

        <discard-prompt ref="discardProject" @leavebypass="bypassLeaveHandler = true" />
    </sui-grid>
</template>

<script>
/* eslint-disable max-lines */
import ConfirmationForm from '../../components/Projects/ConfirmationForm';
import ConfirmationText from '../../components/Projects/ConfirmationText';
import DiscardPrompt from '../../components/Projects/DiscardPrompt.vue';
import DomainForm from '../../components/Projects/Apps/DomainForm';
import ProgressModal from '../../components/ProgressModal';
import RedirectForm from '../../components/Projects/Apps/RedirectForm';
import SourceSelector from '../../components/Projects/Apps/SourceSelector';
import StepList from '../../components/Projects/StepList';
import TemplateSelector from '../../components/Projects/Apps/TemplateSelector';
import providers from './source-providers.json';
import steps from './steps.json';
import templates from './templates.json';

const PERCENT_APP = 25,
    PERCENT_REDIRECT = 40,
    STEP_APP = 'app.save',
    STEP_CREATE = 'project.create',
    STEP_REDIRECT = 'redirect.save',
    VALIDATION_ERROR = 422;

export default {
    components: {
        ConfirmationForm,
        ConfirmationText,
        DiscardPrompt,
        DomainForm,
        ProgressModal,
        RedirectForm,
        StepList,
        SourceSelector,
        TemplateSelector,
    },
    data() {
        return {
            bypassLeaveHandler: false,
            error: '',
            errors: {},
            extraData: {
                repository: '',
                provider: '',
            },
            project: {
                name: '',
                applications: [],
                redirects: [],
            },
            providers,
            step: 'template',
            steps,
            templates,
        };
    },
    beforeRouteLeave(to, from, next) {
        if (this.bypassLeaveHandler) {
            this.bypassLeaveHandler = false;
            next();
        } else {
            this.$refs.discardProject.prompt(() => {
                this.discard();
                next();
            }, () => next(false));
        }
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
        defaultRedirect: {
            get() {
                return this.project.redirects[0];
            },
            set(data) {
                Vue.set(this.project.redirects, 0, data);
            },
        },
        template() {
            return this.templates.find(t => t.name.toLowerCase() === this.defaultApp.template);
        },
    },
    methods: {
        cancel() {
            this.$refs.discardProject.prompt(this.discard, () => {
                this.$refs.discardProject.hide();
            }, 'projects');
        },
        discard() {
            this.extraData = {
                repository: '',
            };

            this.project = {
                name: '',
                applications: [],
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
        jumpToFirstError() {
            for (const step of this.steps) {
                const keys = 'errorKeys' in step ? step.errorKeys : [ step.errorKey ];

                for (const field of keys) {
                    if (field in this.errors && !step.disabled) {
                        this.step = step.name;

                        return;
                    }
                }
            }
        },
        nextStep(from) {
            const step = this.template.steps.findIndex(s => s === from);

            this.goto(this.template.steps[step + 1]);
        },
        setAppTemplate(tpl) {
            const [ firstStep ] = tpl.steps;

            if (tpl.isApp) {
                this.project.applications.push({
                    template: tpl.name.toLowerCase(),
                    provider: 'github',
                    domain: '',
                });
            } else {
                this.project.applications.push({ template: 'archive', domain: '' });
                this.project.redirects.push({
                    domain: '',
                    target: '',
                    type: 301,
                });
            }

            this.steps.forEach(s => {
                if (tpl.steps.includes(s.name)) {
                    s.disabled = false;
                }
            });

            this.goto(firstStep);
        },
        setAppSource(source) {
            const { branch, provider, repository, repoUri } = source;

            this.defaultApp = { ...this.defaultApp, repository, provider, branch };
            this.extraData = { repoUri };

            this.nextStep('source');
        },
        setRedirect(redirect) {
            this.defaultRedirect = { ...this.defaultRedirect, ...redirect };

            this.nextStep('redirect');
        },
        async create(enabled = false) {
            this.setErrors({}, '');

            const [channel, step, project] = [
                'projects',
                this.progressInit(),
                await this.createProject(this.project.name, enabled),
            ];

            if (null === project) {
                return;
            }

            await Promise.all([
                this.$store.dispatch('progress/monitor', { channel, item: project.id }),
                this.$store.dispatch('progress/progress', { progress: 10 }),
            ]);

            try {
                await this.createAppOrRedirect(project, step);

                await this.$store.dispatch('progress/activateContinueButton', {
                    name: 'projects.view',
                    params: { id: project.id },
                });
            } catch (error) {
                this.handleCreationError(step, error);
            } finally {
                this.bypassLeaveHandler = true;
            }
        },
        async createProject(name, isEnabled) {
            try {
                const project = await this.$store.dispatch(
                    'projects/createProject',
                    { name, is_enabled: isEnabled },
                );

                this.$store.dispatch('progress/stepCompleted', { step: STEP_CREATE, progress: 8 });

                return project;
            } catch (error) {
                this.handleCreationError(STEP_CREATE, error);

                return null;
            }
        },
        async createAppOrRedirect(project, step) {
            const [action, data, progress] = step === STEP_APP
                ? ['createApp', { app: this.project.applications[0] }, PERCENT_APP]
                : ['createRedirect', { redirect: this.project.redirects[0] }, PERCENT_REDIRECT];

            await Promise.all([
                this.$store.dispatch('progress/progress', { progress }),
                this.$store.dispatch(`projects/${action}`, { projectId: project.id, ...data }),
            ]);
        },
        handleCreationError(step, error) {
            const { response: res } = error,
                canBeFixed = res && VALIDATION_ERROR === res.status,
                fallback = { route: { name: 'projects' }, text: 'Back to Projects' };

            this.$store.dispatch('progress/stepFailed', { step, canBeFixed, fallback });

            if (canBeFixed) {
                this.setErrors(res.data.errors, 'Please fix the highlighted issues and try again.');

                this.jumpToFirstError();

                return;
            }

            this.error = res && 'statusText' in res ? res.statusText : error.message;
        },
        progressInit() {
            const [ { template } ] = this.project.applications,
                isApp = 'archive' !== template,
                step = isApp ? STEP_APP : STEP_REDIRECT,
                text = isApp ? `${template} application` : 'redirect';

            this.$store.dispatch('progress/load', {
                title: 'Saving project...',
                completeWhenDone: step,
                steps: [
                    { name: STEP_CREATE, text: 'Creating project' },
                    { name: step, text: `Saving the ${text}` },
                ],
            });

            return step;
        },
        setErrors(errors, message) {
            this.errors = errors;
            this.error = message;
        },
    },
};
</script>
