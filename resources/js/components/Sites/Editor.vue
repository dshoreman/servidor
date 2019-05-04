<template>
    <sui-container>
        <h2 is="sui-header">
            {{ site.name }}
            <sui-header-subheader>
                {{ site.primary_domain }}
            </sui-header-subheader>
        </h2>
        <router-link is="sui-button" :to="{ name: 'apps' }" content="Back" />
    </sui-container>
</template>

<script>
export default {
    props: {
        site: {
            type: Object,
            default: () => ({}),
        },
    },
}
</script>
