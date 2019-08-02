<template>
    <sui-container class="grid">
        <sui-grid-row>
            <sui-grid-column>
                <sui-input placeholder="Type a name for your Application..."
                        :icon="filterIcon" class="fluid massive"
                        v-model="site.name" @input="filterSites" @keyup.enter="createOrEdit"></sui-input>
            </sui-grid-column>
        </sui-grid-row>
        <router-view :sites="filteredSites"></router-view>
    </sui-container>
</template>

<script>
import { mapState, mapGetters, mapMutations } from 'vuex';
import store from '../store';

export default {
    beforeRouteEnter (to, from, next) {
        store.dispatch('loadSites').then(() => next());
    },
    computed: {
        ...mapState({
            site: state => state.Site.site,
        }),
        ...mapGetters([
            'sites',
            'filteredSites',
        ]),
        filterIcon: function () {
            const match = this.site.name.toLowerCase();

            if (match == '') {
                return 'search';
            }

            if ('object' == typeof(this.filteredSites.find(s => s.name.toLowerCase() == match))) {
                return 'cogs';
            }

            return 'plus';
        },
    },
    methods: {
        ...mapMutations({
            filterSites: 'setFilter',
        }),
        createOrEdit: function () {
            const match = this.site.name.toLowerCase();

            if (match == '') {
                return;
            }

            let result = this.filteredSites.find(s => s.name.toLowerCase() == match);

            if (typeof(result) === 'object') {
                return this.$router.push({ name: 'apps.edit', params: { id: result.id }});
            }

            this.$store.dispatch('createSite');
        },
    }
}
</script>
