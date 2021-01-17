<template>
    <sui-grid container>
        <sui-grid-row>
            <sui-grid-column>
                <sui-input placeholder="Type a name for your Application..." icon="plus"
                           class="fluid massive" :inverted="darkMode" :transparent="darkMode"
                           v-model="site.name" @keyup.enter="createOrEdit" />
            </sui-grid-column>
        </sui-grid-row>
        <router-view id="sites" :sites="sites" />
    </sui-grid>
</template>

<script>
import { mapGetters, mapState } from 'vuex';
import store from '../store';

export default {
    beforeRouteEnter(to, from, next) {
        store.dispatch('sites/load').then(() => next());
    },
    computed: {
        ...mapState({
            site: state => state.sites.site,
        }),
        ...mapGetters({
            sites: 'sites/all',
        }),
    },
    methods: {
        createOrEdit() {
            const match = this.site.name.toLowerCase();

            if ('' === match) {
                return;
            }

            const result = this.sites.find(s => s.name.toLowerCase() === match);

            if ('object' === typeof result) {
                this.$router.push({ name: 'apps.edit', params: { id: result.id }});

                return;
            }

            this.$store.dispatch('sites/create').then(({ data }) => {
                this.$router.push({ name: 'apps.edit', params: { id: data.id }});
            });
        },
    },
};
</script>
