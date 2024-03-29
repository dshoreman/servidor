<template>
    <sui-grid>
        <sui-grid-column :width="16" v-if="error">
            <sui-message negative :content="error" header="Couldn't create the project!" />
        </sui-grid-column>
        <sui-grid-column :width="6">
            <step-list :selected="step" :steps="steps" />
        </sui-grid-column>

        <sui-grid-column :width="10">

            <sui-segment v-if="step == 'template'" :inverted="darkMode">
                <h3 is="sui-header">First pick a template to get started</h3>
                <sui-message negative v-if="'template' in errors"
                    :content="errors['template'][0]" />
                <template-selector :templates="templates"
                    @selected="setAppTemplate" :inverted="darkMode" />
            </sui-segment>

            <sui-segment v-else-if="step == 'phpver'" :inverted="darkMode">
                <h3 is="sui-header">If your project requires an older PHP, set it here</h3>
                <php-versions :errors="errors" v-model="defaultApp.config.phpVersion"
                    @next="nextStep('phpver')" @cancel="cancel" />
            </sui-segment>

            <sui-segment v-else-if="step == 'source'" :inverted="darkMode">
                <h3 is="sui-header">Where are the project files stored?</h3>
                <source-selector :errors="errors" :providers="providers"
                    v-model="defaultApp" @next="nextStep('source')" @cancel="cancel" />
            </sui-segment>

            <sui-segment v-else-if="step == 'domain'" :inverted="darkMode">
                <h3 is="sui-header">Set the main entry point for your app</h3>
                <domain-form :errors="errors" v-model="defaultApp"
                    @next="nextStep('domain')" @cancel="cancel" />
            </sui-segment>

            <sui-segment v-else-if="step == 'ssl'" :inverted="darkMode">
                <h3 is="sui-header">Have an SSL Certificate to use?</h3>
                <ssl-form :errors="errors" v-model="defaultApp.config"
                    @next="nextStep('ssl')" @cancel="cancel" />
            </sui-segment>

            <sui-segment v-else-if="step == 'redirect'" :inverted="darkMode">
                <h3 is="sui-header">Time to set the target!</h3>
                <redirect-form :errors="errors" :domain="defaultApp.domain"
                    @next="setRedirect" @cancel="cancel" />
            </sui-segment>

            <sui-segment padded aligned="center" v-else-if="step == 'confirm'" :inverted="darkMode">
                <h3 is="sui-header">Let's get this Project started!</h3>
                <confirmation-text :service="defaultApp" :providers="providers" />
                <confirmation-form :errors="errors" v-model="project.name"
                                   :template="defaultApp.template"
                                   @created="create" :created-id="projectCreatedId" />
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
import DomainForm from '../../components/Projects/Services/DomainForm';
import PhpVersions from '../../components/Projects/Services/PhpVersions';
import ProgressModal from '../../components/ProgressModal';
import RedirectForm from '../../components/Projects/Services/RedirectForm';
import SourceSelector from '../../components/Projects/Services/SourceSelector';
import SslForm from '../../components/Projects/Services/SslForm';
import StepList from '../../components/Projects/StepList';
import TemplateSelector from '../../components/Projects/Services/TemplateSelector';
import providers from './source-providers.json';
import steps from './steps.json';
import templates from './templates.json';

const PERCENT_REDIRECT = 95,
    PERCENT_SERVICE = 95,
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
        PhpVersions,
        ProgressModal,
        RedirectForm,
        SslForm,
        StepList,
        SourceSelector,
        TemplateSelector,
    },
    data() {
        return {
            bypassLeaveHandler: false,
            error: '',
            errors: {},
            project: { name: '', services: []},
            projectCreatedId: 0,
            providers,
            step: 'template',
            steps,
            templates,
        };
    },
    beforeRouteLeave(to, from, next) {
        if (this.bypassLeaveHandler) {
            this.bypassLeaveHandler = false;
            this.discard();
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
                return this.project.services[0];
            },
            set(data) {
                Vue.set(this.project.services, 0, data);
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
            this.project = { name: '', services: []};

            this.steps.forEach(s => {
                if ('template' !== s.name) {
                    s.completed = false;
                    s.disabled = true;
                }
            });
            this.step = 'template';
            this.$store.dispatch('progress/hide');
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
            const [ firstStep ] = tpl.steps,
                config = tpl.isApp
                    ? { source: { provider: 'github' }}
                    : { redirect: { target: '', type: 301 }};

            this.project.services.push({
                config, domain: '', template: tpl.name.toLowerCase(),
            });

            if (tpl.steps.includes('phpver')) {
                this.defaultApp.config.phpVersion = '8.1';
            }

            this.steps.forEach(s => {
                if (tpl.steps.includes(s.name)) {
                    s.disabled = false;
                }
            });

            this.goto(firstStep);
        },
        setRedirect(redirect) {
            const { domain, target, type } = redirect;

            this.defaultApp.domain = domain;
            this.defaultApp.config.redirect = { target, type };

            this.nextStep('redirect');
        },
        async create(enabled = false) {
            this.setErrors({}, '');

            const [step, project] = [
                this.progressInit(),
                await this.createProject(this.project.name, enabled),
            ];

            if (null === project) {
                return;
            }

            try {
                await this.createService(project, step);

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
            if (this.projectCreatedId) {
                return this.createdProject();
            }

            try {
                const channel = 'projects', project = await this.$store.dispatch(
                    'projects/createProject',
                    { name, is_enabled: isEnabled },
                );

                await this.$store.dispatch('progress/monitor', { channel, item: project.id });
                this.$store.dispatch('progress/stepCompleted', { step: STEP_CREATE, progress: 15 });
                this.projectCreatedId = project.id;

                return project;
            } catch (error) {
                this.handleCreationError(STEP_CREATE, error);

                return null;
            }
        },
        createdProject() {
            this.$store.dispatch('progress/stepCompleted', { step: STEP_CREATE, progress: 15 });

            return this.$store.getters['projects/find'](this.projectCreatedId);
        },
        async createService(project, step) {
            const progress = step === STEP_APP ? PERCENT_SERVICE : PERCENT_REDIRECT;

            await this.$store.dispatch('progress/stepStarted', { step });
            await this.$store.dispatch('projects/createService', {
                projectId: project.id, service: this.defaultApp,
            });
            await this.$store.dispatch('progress/stepCompleted', { step, progress });
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
            const [ { template } ] = this.project.services,
                isApp = 'archive' !== template,
                step = isApp ? STEP_APP : STEP_REDIRECT,
                text = isApp ? `${template} application` : 'redirect';

            this.$store.dispatch('progress/load', {
                title: 'Saving project...',
                steps: [
                    { name: STEP_CREATE, text: 'Creating project', status: 'working' },
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
