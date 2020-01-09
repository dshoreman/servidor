<template>
    <sui-container fluid>
        <alerts :alerts="alerts" />
        <sui-card-group :items-per-row="4">
            <site-item v-for="site in sites" :key="site.id" :site="site"></site-item>
        </sui-card-group>
    </sui-container>
</template>

<script>
import Alerts from '../../components/Alerts';
import SiteItem from '../../components/Sites/SiteItem';
import { mapState } from 'vuex';

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
            alerts: state => state.sites.alerts,
            errors: state => state.sites.errors,
        }),
    },
};
</script>
