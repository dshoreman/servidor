<template>
    <sui-grid container>
        <sui-grid-row>
            <sui-grid-column>
                <sui-input placeholder="Type a name for your Application..." :icon="filterIcon"
                           class="fluid massive" :inverted="darkMode" :transparent="darkMode"
                           v-model="site.name" @input="filterSites" @keyup.enter="createOrEdit"></sui-input>
            </sui-grid-column>
        </sui-grid-row>
        <router-view id="sites" :sites="sites" />
    </sui-grid>
</template>

<script>
import { mapGetters, mapMutations, mapState } from 'vuex';
import store from '../store';

export default {
    beforeRouteEnter (to, from, next) {
        store.dispatch('sites/load').then(() => next());
    },
    computed: {
        ...mapState({
            site: state => state.sites.site,
        }),
        ...mapGetters({
            sites: 'sites/filtered',
        }),
        filterIcon: function () {
            const match = this.site.name.toLowerCase();

            if ('' === match) {
                return 'search';
            }

            if ('object' === typeof(this.sites.find(s => s.name.toLowerCase() === match))) {
                return 'cogs';
            }

            return 'plus';
        },
    },
    methods: {
        ...mapMutations({
            filterSites: 'sites/setFilter',
        }),
        createOrEdit: function () {
            const match = this.site.name.toLowerCase();

            if ('' === match) {
                return;
            }

            let result = this.sites.find(s => s.name.toLowerCase() === match);

            if ('object' === typeof(result)) {
                return this.$router.push({ name: 'apps.edit', params: { id: result.id }});
            }

            this.$store.dispatch('sites/create').then(({data})=>{
                this.$router.push({ name: 'apps.edit', params: {id: data.id}});
            });
        },
    }
};
</script>
