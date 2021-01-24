<template>
    <sui-card-content>
        <h3 is="sui-header">

            <sui-icon
                :name="template.icon"
                :color="template.colour"
                size="big" />

            <sui-header-content>
                {{ template.name }}
                <sui-header-subheader>
                    {{ template.text }}
                </sui-header-subheader>
            </sui-header-content>

        </h3>
    </sui-card-content>
</template>

<script>
export default {
    props: {
        template: {
            type: Object,
            default: () => ({}),
        },
    },
};
</script>
