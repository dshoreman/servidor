<template>
    <sui-grid-row>
        <sui-grid-column :width=4>
            <sui-card-group>
                <site-item v-for="site in sites" :key="site.id" :site="site"></site-item>
            </sui-card-group>
        </sui-grid-column>
        <sui-grid-column :width=12>
            <site-editor :site="site" />
        </sui-grid-column>
    </sui-grid-row>
</template>

<script>
import SiteEditor from '../../components/Sites/Editor';
import SiteItem from '../../components/Sites/SiteItem';
import store from '../../store';

export default {
    components: {
        SiteEditor,
        SiteItem,
    },
    props: {
        id: {
            type: Number,
            default: 0,
        },
        sites: Array,
    },
    beforeRouteEnter (to, from, next) {
        next(vm => vm.edit(to.params.id));
    },
    beforeRouteUpdate (to, from, next) {
        this.edit(to.params.id);
        next();
    },
    computed: {
        site(){
            return this.sites.find(s => s.id === this.id);
        },
    },
    methods: {
        edit(id) {
            return store.dispatch('editSite', parseInt(id));
        },
    },
}
</script>
