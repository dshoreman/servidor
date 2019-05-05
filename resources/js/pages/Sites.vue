<template>
    <sui-container class="grid">
        <sui-grid-row>
            <sui-grid-column>
                <sui-input placeholder="Type a name for your Application..."
                        icon="plus" class="fluid massive"
                        v-model="site.name" @keyup.enter="create"></sui-input>
            </sui-grid-column>
        </sui-grid-row>
        <router-view :sites="sites"></router-view>
    </sui-container>
</template>

<script>
import { mapState, mapGetters, mapActions, mapMutations } from 'vuex';

export default {
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
