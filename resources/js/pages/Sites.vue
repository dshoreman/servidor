<template>
    <sui-container class="grid">
        <sui-grid-row>
            <sui-grid-column>
                <sui-input placeholder="Type a name for your Application..."
                        icon="plus" class="fluid massive"
                        v-model="site.name" @input="filterSites" @keyup.enter="create"></sui-input>
            </sui-grid-column>
        </sui-grid-row>
        <router-view :sites="filteredSites"></router-view>
    </sui-container>
</template>

<script>
import { mapState, mapGetters, mapActions, mapMutations } from 'vuex';
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
    },
    methods: {
        ...mapActions({
            create: 'createSite',
        }),
        ...mapMutations({
            filterSites: 'setFilter',
        }),
    }
}
</script>
