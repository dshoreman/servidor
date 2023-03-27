<template>
    <sui-form @submit.prevent="$emit('created', true)" :inverted="darkMode">

        <sui-divider />

        <sui-grid textAlign="center">
            <sui-grid-column centered :width="11">
                <sui-form-field :error="'name' in errors">
                    <label>Give your project a name:</label>
                    <sui-input :value="value" :disabled="0 < createdId"
                        placeholder="My new Blog"
                        @input="$emit('input', $event)" />
                    <sui-label basic color="red" pointing v-if="'name' in errors">
                        {{ errors.name[0] }}
                    </sui-label>
                </sui-form-field>
            </sui-grid-column>
        </sui-grid>

        <sui-divider hidden />

        <sui-button color="green" inverted size="big" v-if="darkMode && template == 'Clean Slate'">
            Create Project
        </sui-button>
        <sui-button positive size="big" v-else-if="template == 'Clean Slate'">
            Create Project
        </sui-button>
        <div v-else>
            <sui-button color="green" inverted size="big" v-if="darkMode">
                Save and start the {{ template }} application
            </sui-button>
            <sui-button positive size="big" v-else>
                Save and start the {{ template }} application
            </sui-button>

            <sui-divider horizontal :inverted="darkMode">Or</sui-divider>

            <sui-button primary type="button" :inverted="darkMode"
                        content="Just save the project"
                        @click="$emit('created', false)" />
        </div>
    </sui-form>

</template>

<script>
export default {
    props: {
        createdId: { type: Number, default: 0 },
        errors: { type: Object, default: () => ({}) },
        template: { type: String, default: '' },
        value: { type: String, default: '' },
    },
};
</script>
