<template>
    <sui-grid-row>
        <sui-grid-column :width=4>
            <sui-card-group>
                <site-item v-for="site in sites" :key="site.id" :site="site"></site-item>
            </sui-card-group>
        </sui-grid-column>
        <sui-grid-column :width=12>
            <h2 is="sui-header" size="huge" :inverted="darkMode">
                <router-link :to="{ name: 'apps.edit', params: { id: site.id } }"
                             content="Manage Site" is="sui-button" floated="right"
                             color="teal" icon="cogs" size="large" />
                {{ site.name }}
                <sui-header-subheader>
                    {{ site.primary_domain }}
                </sui-header-subheader>
            </h2>

            <sui-header attached="top" :inverted="darkMode">Source Files</sui-header>
            <sui-segment attached :inverted="darkMode">
                <sui-grid>
                    <sui-grid-row :columns="2">
                        <sui-grid-column>
                            <sui-header size="tiny" :inverted="darkMode">
                                Repository Clone URL
                                <sui-header-subheader v-if="site.source_repo">
                                    {{ site.source_repo }}
                                </sui-header-subheader>
                                <sui-header-subheader v-else>
                                    No source repository has been set for this project!
                                </sui-header-subheader>
                            </sui-header>
                        </sui-grid-column>
                        <sui-grid-column>
                            <sui-header size="tiny" :inverted="darkMode"> Tracking Branch
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

                <sui-header size="tiny" :inverted="darkMode" v-if="site.project_root">
                    <router-link :to="{ name: 'files', params: {path: site.project_root } }"
                                 content="Browse files" is="sui-button" floated="right"
                                 basic primary icon="open folder" />
                    Project Root
                    <sui-header-subheader>{{ site.project_root }}</sui-header-subheader>
                </sui-header>
                <p v-else>This project doesn't have a project root defined.</p>
            </sui-segment>

            <sui-header v-if="site.type == 'redirect'" attached="top" :inverted="darkMode">
                Redirection
            </sui-header>
            <sui-segment v-if="site.type == 'redirect'" attached :inverted="darkMode">
                <sui-grid>
                    <sui-grid-row :columns="2">
                        <sui-grid-column>
                            <sui-header size="tiny" :inverted="darkMode">
                                Target URL
                                <sui-header-subheader>
                                    {{ site.redirect_to }}
                                </sui-header-subheader>
                            </sui-header>
                        </sui-grid-column>
                        <sui-grid-column>
                            <sui-header size="tiny" :inverted="darkMode">
                                Redirect Type
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

            <sui-header attached="top" :inverted="darkMode" v-if="site.system_user">
                System User
            </sui-header>
            <sui-segment attached :inverted="darkMode" v-if="site.system_user">
                <sui-grid>
                    <sui-grid-row>
                        <sui-grid-column :width="8">
                            <sui-header size="tiny" :inverted="darkMode">
                                Username
                                <sui-header-subheader>
                                    {{ site.system_user.name }}
                                </sui-header-subheader>
                            </sui-header>
                        </sui-grid-column>
                        <sui-grid-column :width="4">
                            <sui-header size="tiny" :inverted="darkMode">
                                User ID
                                <sui-header-subheader>
                                    {{ site.system_user.uid }}
                                </sui-header-subheader>
                            </sui-header>
                        </sui-grid-column>
                        <sui-grid-column :width="4">
                            <sui-header size="tiny" :inverted="darkMode">
                                Group ID
                                <sui-header-subheader>
                                    {{ site.system_user.gid }}
                                </sui-header-subheader>
                            </sui-header>
                        </sui-grid-column>
                    </sui-grid-row>
                </sui-grid>

                <sui-header size="tiny" :inverted="darkMode" v-if="site.system_user.dir">
                    <router-link :to="{ name: 'files', params: { path: site.system_user.dir }}"
                                 content="Browse files" is="sui-button" floated="right"
                                 basic primary icon="open folder" />
                    Home Directory
                    <sui-header-subheader>{{ site.system_user.dir }}</sui-header-subheader>
                </sui-header>
            </sui-segment>

            <sui-header attached="top" :inverted="darkMode" v-if="logNames(site).length">
                Project Logs
            </sui-header>
            <sui-segment attached :inverted="darkMode" v-if="logNames(site).length">
                <sui-menu pointing secondary :inverted="darkMode">
                    <a is="sui-menu-item" v-for="(log, key) in site.logs" :key="key"
                        :active="activeLog === key" @click="viewLog(site.id, key)"
                        :content="log.name" />
                </sui-menu>
                <pre>{{ logContent }}</pre>
            </sui-segment>

        </sui-grid-column>
    </sui-grid-row>
</template>

<style>
.logtable tr {
    cursor: pointer;
}
</style>

<script>
import SiteItem from '../../components/Sites/SiteItem';
import { mapGetters } from 'vuex';

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
    data() {
        return {
            activeLog: '',
            logContent: '',
        };
    },
    mounted() {
        this.initLog(this.site.id);
    },
    beforeRouteUpdate(to, from, next) {
        this.initLog(to.params.id);
        next();
    },
    computed: {
        ...mapGetters({
            findSite: 'sites/findById',
        }),
        site() {
            return this.findSite(this.id);
        },
    },
    methods: {
        initLog(siteId) {
            const logs = this.logNames(siteId);

            if (logs.length) {
                this.viewLog(siteId, logs[0]);
            } else {
                this.logContent = '';
                this.activeLog = '';
            }
        },
        logNames(site) {
            const s = 'object' === typeof site ? site : this.findSite(parseInt(site));

            return Object.keys(s.logs);
        },
        viewLog(id, key) {
            this.logContent = 'Loading...';
            this.activeLog = key;

            axios.get(`/api/sites/${id}/logs/${this.activeLog}`).then(response => {
                this.logContent = '' === response.data.trim()
                    ? "Log file is empty or doesn't exist."
                    : response.data;
            }).catch(() => {
                this.logContent = `Failed to load ${this.activeLog} log!`;
            });
        },
    },
};
</script>
