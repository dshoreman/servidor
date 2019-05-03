<template>
    <sui-card>
        <sui-card-content>
            <sui-card-header>{{ site.name }}</sui-card-header>
            <sui-card-meta>{{ site.primary_domain }}</sui-card-meta>
        </sui-card-content>
        <sui-button attached="bottom">
            <sui-icon name="cogs"></sui-icon> Manage Site
        </sui-button>
    </sui-card>
</template>

<script>
export default {
    props: {
        site: Object,
    },
}
</script>
