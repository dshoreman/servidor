<template>
    <sui-form @submit.prevent="$emit('next')" v>

        <sui-form-field :error="'applications.0.domain' in errors">

            <label>Domain name</label>

            <sui-input :value="value"
                placeholder="example.com" required
                @input="$emit('input', $event)" />

            <sui-label basic color="red" pointing
                v-if="'applications.0.domain' in errors">
                {{ errors['applications.0.domain'][0] }}
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
        value: { type: String, default: '' },
    },
};
</script>
