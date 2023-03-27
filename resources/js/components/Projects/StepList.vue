<template>
    <sui-step-group vertical fluid :class="darkMode && 'inverted'">

        <sui-step v-for="s in steps"
                  :active="selected == s.name"
                  :completed="s.completed"
                  :disabled="s.disabled"
                  :title="s.title"
                  :key="s.title"
                  :icon="s.icon" />

    </sui-step-group>
</template>

<script>
export default {
    props: {
        selected: {
            type: String,
            default: '',
        },
        steps: {
            type: Array,
            default: () => [],
        },
    },
};
</script>
