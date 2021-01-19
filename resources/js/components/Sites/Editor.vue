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

            <sui-header content="System User" />
            <sui-segment :inverted="darkMode" v-if="!tmpSite.system_user">
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
import Alerts from '../Alerts';
import { mapState } from 'vuex';

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
        }),
    },
    data() {
        return {
            clonedSite: {},
        };
    },
    methods: {
        updateSite(id) {
            this.$store.dispatch('sites/update', { id, data: this.tmpSite });
        },
    },
};
</script>
