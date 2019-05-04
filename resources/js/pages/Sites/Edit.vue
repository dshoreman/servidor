<template>
    <sui-grid-row>
        <sui-grid-column :width=4>
            <sui-card-group>
                <site-item v-for="site in sites" :key="site.id" :site="site"></site-item>
            </sui-card-group>
        </sui-grid-column>
        <sui-grid-column :width=8>
            <site-editor :site="site" />
        </sui-grid-column>
    </sui-grid-row>
</template>

<script>
import { mapGetters } from 'vuex';
import SiteEditor from '../../components/Sites/Editor';
import SiteItem from '../../components/Sites/SiteItem';

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
    },
    computed: {
        ...mapGetters([
            'sites',
        ]),
        site(){
            return this.sites.find(s => s.id === this.id);
        },
    },
}
</script>
