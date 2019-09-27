<template>
    <sui-grid-row>
        <sui-grid-column :width=4>
            <sui-card-group>
                <site-item v-for="site in sites" :key="site.id" :site="site"></site-item>
            </sui-card-group>
        </sui-grid-column>
        <sui-grid-column :width=12>
            <h2 is="sui-header" size="huge">
                <router-link :to="{ name: 'apps.edit', params: { id: site.id } }"
                             content="Manage Site" is="sui-button" floated="right"
                             color="teal" icon="cogs" size="large" />
                {{ site.name }}
                <sui-header-subheader>
                    {{ site.primary_domain }}
                </sui-header-subheader>
            </h2>

            <sui-header attached="top">Source Files</sui-header>
            <sui-segment attached>
                <sui-grid>
                    <sui-grid-row :columns="2">
                        <sui-grid-column>
                            <sui-header size="tiny"> Repository Clone URL
                                <sui-header-subheader v-if="site.source_repo">
                                    {{ site.source_repo }}
                                </sui-header-subheader>
                                <sui-header-subheader v-else>
                                    No source repository has been set for this project!
                                </sui-header-subheader>
                            </sui-header>
                        </sui-grid-column>
                        <sui-grid-column>
                            <sui-header size="tiny"> Tracking Branch
                                <sui-button basic positive icon="download" floated="right"
                                    content="Pull Latest Code" @click="pullFiles(site)" />
                                <sui-header-subheader v-if="site.source_branch">
                                    {{ site.source_branch }}
                                </sui-header-subheader>
                                <sui-header-subheader v-else>
                                    Using default branch
                                </sui-header-subheader>
                            </sui-header>
                        </sui-grid-column>
                    </sui-grid-row>
                </sui-grid>

                <sui-header size="tiny" v-if="site.document_root">
                    <router-link :to="{ name: 'files', params: {path: site.document_root } }"
                                 content="Browse files" is="sui-button" floated="right"
                                 basic primary icon="open folder" />
                    Document Root
                    <sui-header-subheader>{{ site.document_root }}</sui-header-subheader>
                </sui-header>
                <p v-else>This project doesn't have a document root defined.</p>
            </sui-segment>

            <sui-header v-if="site.type == 'redirect'" attached="top">Redirection</sui-header>
            <sui-segment v-if="site.type == 'redirect'" attached>
                <sui-grid>
                    <sui-grid-row :columns="2">
                        <sui-grid-column>
                            <sui-header size="tiny"> Target URL
                                <sui-header-subheader>{{ site.redirect_to }}</sui-header-subheader>
                            </sui-header>
                        </sui-grid-column>
                        <sui-grid-column>
                            <sui-header size="tiny"> Redirect Type
                                <sui-header-subheader v-if="site.redirect_type == 301">
                                    Permanent
                                </sui-header-subheader>
                                <sui-header-subheader v-else-if="site.redirect_type == 302">
                                    Temporary
                                </sui-header-subheader>
                                <sui-header-subheader v-else>
                                    {{ site.redirect_type }}
                                </sui-header-subheader>
                            </sui-header>
                        </sui-grid-column>
                    </sui-grid-row>
                </sui-grid>
            </sui-segment>
        </sui-grid-column>
    </sui-grid-row>
</template>

<script>
import { mapActions } from 'vuex';
import SiteItem from '../../components/Sites/SiteItem';

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
    methods: {
        ...mapActions({
            pullFiles: 'pullSiteFiles',
        }),
    },
}
</script>
