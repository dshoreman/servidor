<template>
    <sui-form @submit.prevent="submit()">

        <sui-form-fields inline>
            <label>Source Provider</label>
            <sui-form-field v-for="p in providers" :key="p.name">
                <sui-checkbox radio v-model="provider" required
                    :value="p.name" :label="p.text" />
            </sui-form-field>
        </sui-form-fields>

        <sui-form-field v-if="provider == 'custom'">
            <label>Repository URL:</label>
            <sui-input v-model="url" required />
        </sui-form-field>

        <sui-form-field v-else>
            <label>Repository:</label>
            <sui-input placeholder="dshoreman/servidor-test-site" required
                v-model="repository" @change="loadBranches(repository)" />
        </sui-form-field>

        <sui-form-field>
            <label>Deployment Branch:</label>
            <sui-dropdown search selection :loading="branchesLoading" required
                :options="branchOptions" v-model="branch" placeholder="Select branch..." />
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
    props: [ 'providers' ],
    data() {
        return {
            branch: '',
            branches: [],
            branchesLoading: false,
            provider: 'github',
            repository: '',
            url: '',
        };
    },
    computed: {
        branchOptions() {
            return this.branches.map(b => ({ text: b, value: b }));
        },
    },
    methods: {
        loadBranches(repo) {
            if ('' === repo) {
                this.branches = [];

                return;
            }

            this.branchesLoading = true;

            axios.get(`/api/system/git/branches?provider=${this.provider}&repository=${repo}`).then(
                response => {
                    this.branches = response.data;
                    this.branchesLoading = false;
                },
            );
        },
        submit() {
            let repoUri = this.repository;

            const providerOpts = this.providers.find(p => p.name === this.provider),
                { repository, branch, provider } = this;

            if ('urlFormat' in providerOpts) {
                repoUri = providerOpts.urlFormat.replace('%REPO%', repository);
            }

            this.$emit('selected', { branch, provider, repository, repoUri });
        },
    },
};
</script>
