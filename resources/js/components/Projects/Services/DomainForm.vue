<template>
    <sui-form :inverted="darkMode" @submit.prevent="save()">

        <sui-form-field :error="'domain' in errors">

            <label>Domain name</label>

            <sui-input :value="value.domain"
                placeholder="example.com" required
                @input="setValue('domain', $event)" />

            <sui-label basic color="red" pointing
                v-if="'domain' in errors">
                {{ errors['domain'][0] }}
            </sui-label>

        </sui-form-field>

        <sui-form-field>
            <label>Prefix Preferences</label>
            <sui-form-fields inline>
                <sui-form-field :width="isNotRedirect() ? 8 : 16">
                    <sui-checkbox toggle :checked="value.includeWww"
                        :disabled="value.domain.startsWith('www.')"
                        @change="setValue('includeWww', $event)"
                        label="Handle 'www.' subdomain" />
                </sui-form-field>
                <sui-form-field :width="8" v-if="isNotRedirect()" style="display: block">
                    <sui-dropdown :options="redirectOptions" :value="value.config.redirectWww"
                        :disabled="!(value.domain.startsWith('www.') || value.includeWww)"
                        fluid selection @input="setConfig('redirectWww', $event)" />
                </sui-form-field>
            </sui-form-fields>
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
        value: { type: Object, default: () => ({}) },
    },
    data() {
        return {
            redirectOptions: [
                { text: 'Serve both (no preference)', value: 0 },
                { text: 'Redirect www → non-www', value: 1 },
                { text: 'Redirect non-www → www', value: -1 },
            ],
        };
    },
    methods: {
        isNotRedirect() {
            return Object.hasOwn(this.value, 'template') && 'archive' !== this.value.template;
        },
        save() {
            const { value, includeWww } = this;

            this.$emit('next', { includeWww, domain: value });
        },
        setConfig(key, value) {
            this.setValue('config', { ...this.value.config, [key]: value });
        },
        setValue(key, value) {
            this.$emit('input', { ...this.value, [key]: value });
        },
    },
};
</script>
