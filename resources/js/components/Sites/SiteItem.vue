<template>
    <sui-card>
        <sui-card-content>
            <sui-card-header>{{ site.name }}</sui-card-header>
            <sui-card-meta>{{ site.primary_domain }}</sui-card-meta>
        </sui-card-content>

        <sui-card-content extra>
            <router-link :to="{ name: 'apps.edit', params: { id: site.id } }">
                <sui-icon name="cogs"></sui-icon> Manage Site
            </router-link>
        </sui-card-content>
    </sui-card>
</template>

<script>
export default {
    props: {
        site: Object,
    },
}
</script>
