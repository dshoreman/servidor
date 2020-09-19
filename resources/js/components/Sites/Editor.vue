<template>
    <sui-container>
        <sui-form @submit.prevent="updateSite(tmpSite.id)" :inverted="darkMode">
            <sui-form-field class="enable-switch">
                <sui-checkbox toggle v-model="tmpSite.is_enabled"/>
            </sui-form-field>

            <h2 is="sui-header">
                Editing {{ site.name }}
                <sui-header-subheader>
                    {{ site.primary_domain }}
                </sui-header-subheader>
            </h2>

            <alerts :alerts="alerts" />

            <sui-form-field :error="'name' in errors">
                <label>App Name</label>
                <sui-input v-model="tmpSite.name" @input="setDocroot" placeholder="My Blog" />
                <sui-label basic color="red" pointing v-if="'name' in errors">
                    {{ errors.name[0] }}
                </sui-label>
            </sui-form-field>

            <sui-header content="Primary Domain" />
            <sui-form-fields>
                <sui-form-field :width="12" :error="'primary_domain' in errors">
                    <label>Domain Name</label>
                    <sui-input placeholder="example.com" @input="setDocroot"
                        v-model="tmpSite.primary_domain" />
                        <sui-label basic color="red" pointing
                            v-if="'primary_domain' in errors">
                            {{ errors.primary_domain[0] }}
                        </sui-label>
                </sui-form-field>
                <sui-form-field :width="4" :error="'type' in errors">
                    <label>Project type</label>
                    <sui-dropdown selection :options="[
                        { text: 'Redirect', value: 'redirect' },
                        { text: 'Basic Website', value: 'basic' },
                        { text: 'PHP Website', value: 'php' },
                        { text: 'Laravel App', value: 'laravel' },
                    ]" v-model="tmpSite.type" />
                    <sui-label basic color="red" pointing v-if="'type' in errors">
                        {{ errors.type[0] }}
                    </sui-label>
                </sui-form-field>
            </sui-form-fields>

            <sui-form-field v-if="tmpSite.type == 'redirect'"
                :error="'redirect_to' in errors">
                <label>Destination</label>
                <sui-input v-model="tmpSite.redirect_to" placeholder="example.com" />
                <sui-label basic color="red" pointing v-if="'redirect_to' in errors">
                    {{ errors.redirect_to[0] }}
                </sui-label>
            </sui-form-field>
            <sui-form-fields inline v-if="tmpSite.type == 'redirect'">
                <label>Redirect Type</label>
                <sui-form-field :error="'redirect_type' in errors">
                    <sui-checkbox radio v-model="tmpSite.redirect_type"
                        label="Temporary" value="302" />
                </sui-form-field>
                <sui-form-field :error="'redirect_type' in errors">
                    <sui-checkbox radio v-model="tmpSite.redirect_type"
                        label="Permanent" value="301" />
                    <sui-label basic color="red" pointing="left"
                        v-if="'redirect_type' in errors">
                        {{ errors.redirect_type[0] }}
                    </sui-label>
                </sui-form-field>
            </sui-form-fields>

            <sui-form-fields v-if="tmpSite.type && tmpSite.type != 'redirect'">
                <sui-form-field :width="12" :error="'source_repo' in errors">
                    <label>Clone URL</label>
                    <sui-input v-model="tmpSite.source_repo"
                        @change="refreshBranches(tmpSite.source_repo)" />
                    <sui-label basic color="red" pointing v-if="'source_repo' in errors">
                        {{ errors.source_repo[0] }}
                    </sui-label>
                </sui-form-field>
                <sui-form-field :width="4" :error="'source_branch' in errors">
                    <label>Branch</label>
                    <sui-dropdown search selection :loading="loadingBranches"
                        :options="branches" v-model="tmpSite.source_branch"
                        placeholder="Select branch..." />
                    <sui-label basic color="red" pointing v-if="'source_branch' in errors">
                        {{ errors.source_branch[0] }}
                    </sui-label>
                </sui-form-field>
            </sui-form-fields>

            <sui-form-field v-if="['basic', 'php', 'laravel'].includes(tmpSite.type)"
                :error="'document_root' in errors">
                <label>Document Root</label>
                <sui-input readonly v-model="tmpSite.document_root" />
                <sui-label basic color="red" pointing v-if="'document_root' in errors">
                    {{ errors.document_root[0] }}
                </sui-label>
            </sui-form-field>

            <sui-header content="System User" />
            <sui-segment :inverted="darkMode" v-if="!tmpSite.system_user">
                <sui-label basic color="red" pointing="below" v-if="'system_user' in errors">
                    {{ errors.system_user[0] }}
                </sui-label>
                <sui-form-field :error="'system_user' in errors">
                    <sui-checkbox toggle v-model="tmpSite.create_user" value="1">
                        Create a user named '<code>{{ tmpSite.name }}</code>' for this project
                    </sui-checkbox>
                </sui-form-field>
            </sui-segment>
            <sui-segment :inverted="darkMode" color="violet" v-else>
                <p>
                    <sui-icon name="check" /> The user
                    <strong>{{ tmpSite.system_user.name }}</strong>
                    exists and is linked to this project.
                </p>
            </sui-segment>

            <sui-button negative icon="trash" type="button"
                content="Delete" floated="right" @click="deleteSite(site.id)" />
            <sui-button-group>
                <router-link :to="{ name: 'apps.view', params: { id: site.id } }" is="sui-button"
                    type="button" content="Cancel" />
                <sui-button-or />
                <sui-button type="submit" positive content="Save" />
            </sui-button-group>
        </sui-form>
    </sui-container>
</template>

<style scoped>
.ui.form .field.enable-switch {
    float: right;
    margin-top: 5px;
}
</style>

<script>
import { mapActions, mapGetters, mapState } from 'vuex';
import Alerts from '../Alerts';

export default {
    props: {
        site: {
            type: Object,
            default: () => ({}),
        },
    },
    components: {
        Alerts,
    },
    computed: {
        ...mapState({
            alerts: state => state.sites.alerts,
            errors: state => state.sites.errors,
            tmpSite: state => state.sites.current,
            loadingBranches: state => state.sites.branchesLoading,
        }),
        ...mapGetters({
            branches: 'sites/branchOptions',
        }),
    },
    watch: {
        'tmpSite.type': function () {
            this.setDocroot();
        },
    },
    data() {
        return {
            clonedSite: {},
        };
    },
    methods: {
        ...mapActions({
            refreshBranches: 'sites/loadBranches',
        }),
        updateSite(id) {
            this.$store.dispatch('sites/update', { id, data: this.tmpSite });
        },
        deleteSite() {
            /* eslint-disable no-alert */
            confirm('Deletion is permanent! Are you sure?')
                && this.$store.dispatch('sites/delete', this.site.id).then(
                    () => this.$router.push({ name: 'apps' }),
                );
        },
        setDocroot() {
            const site = this.tmpSite;
            let val = '';

            if (['basic', 'php', 'laravel'].includes(site.type)) {
                val = `/var/www/${site.primary_domain || _.kebabCase(site.name)}`;

                if ('laravel' === site.type) {
                    val += '/public';
                }
            }

            site.document_root = val;
        },
    },
};
</script>
