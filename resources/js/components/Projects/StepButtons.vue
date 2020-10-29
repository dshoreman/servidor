<template>
    <div>
        <sui-divider hidden />

        <sui-button negative @click="close()"
                    content="Cancel"
                    icon="close"
                    label-position="left"
                    type="button" />

        <sui-button primary floated="right"
                    content="Next"
                    icon="right arrow"
                    label-position="right" />
    </div>
</template>

<script>
export default {
    methods: {
        close() {
            this.$router.push({ name: 'projects' });
        },
    },
};
</script>
