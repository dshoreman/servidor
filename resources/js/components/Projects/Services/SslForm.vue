<template>
    <sui-form @submit.prevent="$emit('next')" :inverted="darkMode">

        <sui-form-field :error="'config.ssl' in errors">
            <sui-checkbox toggle :checked="value.ssl"
                          @change="checked => setConfig('ssl', checked)"
                          label="Enable SSL for this project" />
        </sui-form-field>

        <sui-form-field :error="'config.sslRedirect' in errors">
            <sui-checkbox toggle :disabled="!value.ssl" :checked="value.sslRedirect"
                          @change="checked => setConfig('sslRedirect', checked)"
                          label="Force SSL (redirect HTTP to HTTPS)" />
        </sui-form-field>

        <sui-form-field :disabled="!value.ssl" :error="'config.sslCertificate' in errors">
            <label>SSL Certificate</label>
            <textarea :required="value.ssl" :value="value.sslCertificate"
                @input="e => setConfig('sslCertificate', e.target.value)" />
        </sui-form-field>

        <sui-form-field :disabled="!value.ssl" :error="'config.sslPrivateKey' in errors">
            <label>Private Key</label>
            <textarea :required="value.ssl" :value="value.sslPrivateKey"
                @input="e => setConfig('sslPrivateKey', e.target.value)" />
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
    mounted() {
        if (!Object.hasOwn(this.value, 'ssl')) {
            this.setConfig('ssl', false);
        }
    },
    props: {
        errors: { type: Object, default: () => ({}) },
        value: { type: Object, default: () => ({}) },
    },
    methods: {
        setConfig(key, value) {
            this.$emit('input', { ...this.value, [key]: value });
        },
    },
};
</script>
