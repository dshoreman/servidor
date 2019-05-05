<template>
    <sui-card-group>
        <site-item v-for="site in sites" :key="site.id" :site="site"></site-item>
    </sui-card-group>
</template>

<script>
import SiteItem from '../../components/Sites/SiteItem';

export default {
    components: {
        SiteItem,
    },
    props: [
        'sites',
    ],
}
</script>
