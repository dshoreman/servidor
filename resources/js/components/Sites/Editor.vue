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

            <sui-header content="System User" />
            <sui-segment :inverted="darkMode" color="violet" v-if="tmpSite.system_user">
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
