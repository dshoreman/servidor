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
            <sui-input placeholder="dshoreman/servidor-test-site"
                v-model="repository" required />
        </sui-form-field>

        <sui-form-field>
            <label>Deployment Branch:</label>
            <sui-input v-model="branch" placeholder="master" required />
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
            provider: 'github',
            repository: '',
            url: '',
        };
    },
    methods: {
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
