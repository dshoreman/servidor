<template>
    <sui-form @submit.prevent="$emit('enabled')">

        <sui-divider />

        <sui-grid textAlign="center">
            <sui-grid-column centered :width="11">
                <sui-form-field>
                    <label>Give your project a name:</label>
                    <sui-input :value="value"
                        placeholder="My new Blog"
                        @input="$emit('input', $event)" />
                </sui-form-field>
            </sui-grid-column>
        </sui-grid>

        <sui-divider hidden />

        <sui-button positive size="big">
            Save and start the application
        </sui-button>

        <sui-divider horizontal>Or</sui-divider>

        <sui-button primary type="button"
                    content="Just save the project"
                    @click="$emit('create')" />
    </sui-form>

</template>

<script>
export default {
    props: [ 'value' ],
};
</script>
