<template>
    <router-link is="sui-card" :class="darkMode ? 'inverted' : ''"
        :to="{ name: 'apps.view', params: { id: site.id } }">
        <sui-card-content>
            <sui-card-header>{{ site.name }}</sui-card-header>
            <sui-card-meta>{{ site.primary_domain }}</sui-card-meta>
        </sui-card-content>
    </router-link>
</template>

<script>
export default {
    props: {
        site: Object,
    },
};
</script>
