<template>
    <sui-form @submit.prevent="$emit('next')">

        <sui-form-field :error="'config.ssl' in errors">
            <sui-checkbox toggle v-model="config.ssl"
                                 label="Enable SSL for this project" />
        </sui-form-field>

        <sui-form-field :error="'config.sslCertificate' in errors">
            <label>SSL Certificate</label>
            <textarea required :disabled="!config.ssl"
                v-model="config.sslCertificate" />
        </sui-form-field>

        <sui-form-field :error="'config.sslPrivateKey' in errors">
            <label>Private Key</label>
            <textarea required :disabled="!config.ssl"
                v-model="config.sslPrivateKey" />
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
    },
    data() {
        return {
            config: { ssl: false, sslCertificate: '', sslPrivateKey: '' },
        };
    },
};
</script>
