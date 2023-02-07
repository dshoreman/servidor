<template>
    <sui-form @submit.prevent="save()" v>

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
            <sui-form-fields>
                <sui-form-field :width="8">
                    <sui-checkbox toggle :checked="value.includeWww"
                        :disabled="value.domain.startsWith('www.')"
                        @change="setValue('includeWww', $event)"
                        label="Handle 'www.' subdomain" />
                </sui-form-field>
                <sui-form-field :width="8">
                    <sui-checkbox toggle :checked="value.config.redirectWww"
                        :disabled="!(value.domain.startsWith('www.') || value.includeWww)"
                        @change="setConfig('redirectWww', $event)"
                        label="Auto-remove 'www.' prefix" />
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
            includeWww: false,
            redirectWww: true,
        };
    },
    methods: {
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
