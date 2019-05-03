<template>
    <sui-container class="grid">
        <sui-grid-row>
            <sui-grid-column>
                <sui-input placeholder="Type a name for your Application..."
                        icon="plus" class="fluid massive"
                        v-model="site.name" @keyup.enter="create"></sui-input>
            </sui-grid-column>
        </sui-grid-row>
        <sui-card-group>
            <site-item v-for="site in sites" :key="site.id" :site="site"></site-item>
        </sui-card-group>
    </sui-container>
</template>

<script>
import { mapState, mapGetters, mapActions, mapMutations } from 'vuex';
import SiteItem from '../components/Sites/SiteItem';

export default {
    components: {
        SiteItem,
    },
    mounted () {
        this.$store.dispatch('loadSites');
    },
    computed: {
        ...mapState({
            site: state => state.Site.site,
        }),
        ...mapGetters([
            'sites',
        ]),
    },
    methods: {
        ...mapActions({
            create: 'createSite',
        }),
    }
}
</script>
