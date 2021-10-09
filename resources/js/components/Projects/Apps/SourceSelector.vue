<template>
    <sui-form @submit.prevent="submit()">

        <sui-form-fields inline>
            <label>Source Provider</label>
            <sui-form-field v-for="p in providers" :key="p.name">
                <sui-checkbox radio v-model="values.provider" required
                    :value="p.name" :label="p.text" name="provider" />
            </sui-form-field>
        </sui-form-fields>

        <sui-form-field :error="'repository' in errors" v-if="values.provider === 'custom'">
            <label>Repository URL:</label>
            <sui-input v-model="values.url" required />
            <sui-label basic color="red" pointing v-if="'repository' in errors">
                {{ errors['repository'][0] }}
            </sui-label>
        </sui-form-field>

        <sui-form-field :error="'repository' in errors" v-else>
            <label>Repository:</label>
            <sui-input placeholder="dshoreman/servidor-test-site" required
                v-model="values.repository" @change="loadBranches()" />
            <sui-label basic color="red" pointing v-if="'repository' in errors">
                {{ errors['repository'][0] }}
            </sui-label>
        </sui-form-field>

        <sui-form-field :error="'branch' in errors">
            <label>Deployment Branch:</label>
            <sui-dropdown search selection :loading="branchesLoading" required
                :options="branchOptions" v-model="values.branch" text="Select branch..." />
            <sui-label basic color="red" pointing v-if="'branch' in errors">
                {{ errors['branch'][0] }}
            </sui-label>
        </sui-form-field>

        <step-buttons @cancel="$emit('cancel')" />

    </sui-form>
</template>

<script>
import StepButtons from '../StepButtons';

export default {
    components: {
        StepButtons,
    },
    props: {
        errors: { type: Object, default: () => ({}) },
        providers: { type: Array, default: () => []},
        value: { type: Object, required: true },
    },
    data() {
        return {
            branches: [],
            branchesLoading: false,
        };
    },
    computed: {
        branchOptions() {
            return this.branches.map(b => ({ text: b, value: b }));
        },
        values: {
            get() {
                return this.value;
            },
            set(values) {
                this.$emit('input', values);
            },
        },
    },
    methods: {
        loadBranches() {
            const { provider, repository: repo } = this.value;

            if ('' === repo) {
                this.branches = [];

                return;
            }

            this.branchesLoading = true;

            axios.get(`/api/system/git/branches?provider=${provider}&repository=${repo}`).then(
                response => {
                    this.branches = response.data;
                    this.branchesLoading = false;
                },
            );
        },
        submit() {
            let repoUri = this.values.repository;

            const providerOpts = this.providers.find(p => p.name === this.values.provider),
                { repository, branch, provider } = this.values;

            if ('urlFormat' in providerOpts) {
                repoUri = providerOpts.urlFormat.replace('%REPO%', repository);
            }

            this.$emit('selected', { branch, provider, repository, repoUri });
        },
    },
};
</script>
