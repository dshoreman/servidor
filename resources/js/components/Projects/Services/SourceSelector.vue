<template>
    <sui-form @submit.prevent="$emit('next')" :inverted="darkMode">

        <sui-form-fields inline>
            <label>Source Provider</label>
            <sui-form-field v-for="p in providers" :key="p.name">
                <sui-checkbox radio :inputValue="source.provider" required
                    :value="p.name" @change="setValue('provider', $event)"
                    :label="p.text" name="provider" />
            </sui-form-field>
        </sui-form-fields>

        <sui-form-field :error="'repository' in errors" v-if="source.provider === 'custom'">
            <label>Repository URL:</label>
            <sui-input :value="source.url" @input="setValue('url', $event)" required />
            <sui-label basic color="red" pointing v-if="'repository' in errors">
                {{ errors['repository'][0] }}
            </sui-label>
        </sui-form-field>

        <sui-form-field :error="'repository' in errors" v-else>
            <label>Repository:</label>
            <sui-input placeholder="dshoreman/servidor-test-site" required
                :value="source.repository" @input="setValue('repository', $event)"
                @change="loadBranches()" />
            <sui-label basic color="red" pointing v-if="'repository' in errors">
                {{ errors['repository'][0] }}
            </sui-label>
        </sui-form-field>

        <sui-form-field :error="'branch' in errors">
            <label>Deployment Branch:</label>
            <sui-dropdown search selection :loading="branchesLoading" required
                :options="branchOptions" v-model="source.branch" text="Select branch..." />
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
        source() {
            return this.value.config.source;
        },
    },
    methods: {
        loadBranches() {
            const { provider, repository: repo } = this.value.config.source;

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
        setValue(key, value) {
            this.$emit('input', { ...this.value,
                config: { ...this.value.config,
                    source: { ...this.value.config.source, [key]: value }}});
        },
    },
};
</script>
