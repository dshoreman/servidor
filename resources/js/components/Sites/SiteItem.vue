<template>
    <sui-card>
        <sui-card-content>
            <sui-card-header>{{ name }}</sui-card-header>
            <sui-card-meta>{{ primary_domain }}</sui-card-meta>
        </sui-card-content>
        <router-link is="sui-button" attached="bottom"
            :to="{ name: 'apps.edit', params: { id } }">
            <sui-icon name="cogs"></sui-icon> Manage Site
        </router-link>
    </sui-card>
</template>

<script>
export default {
    props: {
        site: Object,
    },
    data() {
        return {
            id: this.site.id,
            name: this.site.name,
            primary_domain: this.site.primary_domain,
        };
    },
}
</script>
