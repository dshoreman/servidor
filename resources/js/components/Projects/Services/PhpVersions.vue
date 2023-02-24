<template>
    <sui-form @submit.prevent="$emit('next')">

        <sui-form-field :error="'php_version' in errors">
            <sui-dropdown required selection :options="versions"
                v-model="version" @input="handleInput">
                <sui-label basic color="red" pointing v-if="'php_version' in errors">
                    {{ errors['php_version'][0] }}
                </sui-label>
            </sui-dropdown>
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
        value: { type: String, default: '' },
    },
    data() {
        return {
            version: this.value,
            versions: [
                { text: 'PHP 7.0', value: '7.0' },
                { text: 'PHP 7.1', value: '7.1' },
                { text: 'PHP 7.2', value: '7.2' },
                { text: 'PHP 7.3', value: '7.3' },
                { text: 'PHP 7.4', value: '7.4' },
                { text: 'PHP 8.0', value: '8.0' },
                { text: 'PHP 8.1', value: '8.1' },
            ],
        };
    },
    methods: {
        handleInput(value) {
            this.$emit('input', value);
        },
    },
};
</script>
