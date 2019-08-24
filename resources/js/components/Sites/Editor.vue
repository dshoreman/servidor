<template>
    <sui-container>
        <sui-form @submit.prevent="updateSite(tmpSite.id)" :error="error != ''">
            <sui-form-field class="enable-switch">
                <sui-checkbox toggle v-model="tmpSite.is_enabled"/>
            </sui-form-field>

            <h2 is="sui-header">
                Editing {{ site.name }}
                <sui-header-subheader>
                    {{ site.primary_domain }}
                </sui-header-subheader>
            </h2>

            <sui-message v-for="alert in alerts" :key="alert.message"
                         :header="alert.title" v-if="alert.message"
                         :success="alert.isSuccess" :error="!alert.isSuccess"
                         class="visible">
                <p>{{ alert.message }}</p>
            </sui-message>

            <sui-form-field :error="'name' in errors">
                <label>App Name</label>
                <sui-input v-model="tmpSite.name" placeholder="My Blog" />
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
                    <sui-input v-model="tmpSite.source_repo" />
                    <sui-label basic color="red" pointing v-if="'source_repo' in errors">
                        {{ errors.source_repo[0] }}
                    </sui-label>
                </sui-form-field>
                <sui-form-field :width="4" :error="'source_branch' in errors">
                    <label>Branch</label>
                    <sui-input v-model="tmpSite.source_branch" />
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

            <sui-button negative icon="trash" type="button"
                content="Delete" floated="right" @click="deleteSite(site.id)" />
            <sui-button-group>
                <router-link :to="{ name: 'apps' }" is="sui-button"
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
import { mapState } from 'vuex';

export default {
    props: {
        site: {
            type: Object,
            default: () => ({}),
        },
    },
    computed: {
        ...mapState({
            'alerts': state => state.Site.alerts,
            'error': state => state.Site.error,
            'errors': state => state.Site.errors,
            'errorHeader': state => state.Site.error_title,
            'tmpSite': state => state.Site.current,
        }),
    },
    watch: {
        'tmpSite.type'() {
            this.setDocroot();
        },
    },
    data() {
        return {
            clonedSite: {},
        };
    },
    methods: {
        updateSite(id) {
            this.$store.dispatch('updateSite', {id, data: this.tmpSite});
        },
        deleteSite() {
            confirm("Deletion is permanent! Are you sure?") &&
                this.$store.dispatch('deleteSite', this.site.id).then(
                    response => this.$router.push({ name: 'apps' })
                );
        },
        setDocroot() {
            const site = this.tmpSite;
            let val = '';

            if (['basic', 'php', 'laravel'].includes(site.type)) {
                val = '/var/www/' + site.primary_domain;

                if (site.type == 'laravel') {
                    val += '/public';
                }
            }

            site.document_root = val;
        },
    },
}
</script>
