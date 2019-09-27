<template>
    <sui-grid-row>
        <sui-grid-column :width=4>
            <sui-card-group>
                <site-item v-for="site in sites" :key="site.id" :site="site"></site-item>
            </sui-card-group>
        </sui-grid-column>
        <sui-grid-column :width=12>
            <h2 is="sui-header" size="huge">
                {{ site.name }}
                <sui-header-subheader>
                    {{ site.primary_domain }}
                </sui-header-subheader>
            </h2>

            <sui-header attached="top">Source Files</sui-header>
            <sui-segment attached>
                <sui-header size="tiny" v-if="site.source_repo">
                    Repository Clone URL
                    <sui-header-subheader>{{ site.source_repo }}</sui-header-subheader>
                </sui-header>
                <p v-else>No source repository has been set for this project!</p>

                <sui-header size="tiny" v-if="site.document_root">
                    <router-link :to="{ name: 'files', params: {path: site.document_root } }"
                                 content="Browse files" is="sui-button" floated="right"
                                 basic primary icon="open folder" />
                    Document Root
                    <sui-header-subheader>{{ site.document_root }}</sui-header-subheader>
                </sui-header>
                <p v-else>This project doesn't have a document root defined.</p>
            </sui-segment>
        </sui-grid-column>
    </sui-grid-row>
</template>

<script>
import SiteItem from '../../components/Sites/SiteItem';
import store from '../../store';

export default {
    components: {
        SiteItem,
    },
    props: {
        id: {
            type: Number,
            default: 0,
        },
        sites: Array,
    },
    computed: {
        site(){
            return this.sites.find(s => s.id === this.id);
        },
    },
}
</script>
