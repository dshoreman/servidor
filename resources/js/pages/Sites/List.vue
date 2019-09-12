<template>
    <sui-container fluid>
        <alerts :alerts="alerts" />
        <sui-card-group :items-per-row="4">
            <site-item v-for="site in sites" :key="site.id" :site="site"></site-item>
        </sui-card-group>
    </sui-container>
</template>

<script>
import { mapState } from 'vuex';

import Alerts from '../../components/Alerts';
import SiteItem from '../../components/Sites/SiteItem';

export default {
    components: {
        Alerts,
        SiteItem,
    },
    props: [
        'sites',
    ],
    computed: {
        ...mapState({
            alerts: state => state.Site.alerts,
            errors: state => state.Site.errors,
        }),
    },
}
</script>
