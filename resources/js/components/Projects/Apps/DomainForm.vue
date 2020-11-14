<template>
    <sui-form @submit.prevent="$emit('next')" v>

        <sui-form-field>

            <label>Domain name</label>

            <sui-input :value="value"
                placeholder="example.com" required
                @input="$emit('input', $event)" />

        </sui-form-field>

        <step-buttons @cancelled="$emit('cancelled')" />

    </sui-form>
</template>

<script>
import StepButtons from '../StepButtons';

export default {
    components: {
        StepButtons,
    },
    props: [ 'value' ],
};
</script>
