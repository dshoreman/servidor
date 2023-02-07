<template>
    <sui-form @submit.prevent="save()" v>

        <sui-form-field :error="'domain' in errors">

            <label>Domain name</label>

            <sui-input :value="value"
                placeholder="example.com" required
                @input="$emit('input', $event)" />

            <sui-label basic color="red" pointing
                v-if="'domain' in errors">
                {{ errors['domain'][0] }}
            </sui-label>

        </sui-form-field>

        <sui-form-field>
            <label>Prefix Preferences</label>
            <sui-form-fields>
                <sui-form-field :width="8">
                    <sui-checkbox toggle v-model="includeWww"
                        :disabled="this.value.startsWith('www.')"
                        label="Handle 'www.' subdomain" />
                </sui-form-field>
                <sui-form-field :width="8">
                    <sui-checkbox toggle v-model="redirectWww"
                        :disabled="this.value.startsWith('www.') || !this.includeWww"
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
        value: { type: String, default: '' },
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
    },
};
</script>
