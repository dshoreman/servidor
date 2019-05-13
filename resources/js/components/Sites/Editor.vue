<template>
    <sui-container>
        <h2 is="sui-header">
            Editing {{ site.name }}
            <sui-header-subheader>
                {{ site.primary_domain }}
            </sui-header-subheader>
        </h2>

        <sui-form @submit.prevent="updateSite(tmpSite.id)">
            <sui-form-field>
                <label>App Name</label>
                <sui-input v-model="tmpSite.name" placeholder="My Blog" />
            </sui-form-field>

            <sui-header content="Primary Domain" />
            <sui-form-fields>
                <sui-form-field :width="12">
                    <label>Domain Name</label>
                    <sui-input placeholder="example.com" @input="setDocroot"
                        v-model="tmpSite.primary_domain" />
                </sui-form-field>
                <sui-form-field :width="4">
                    <label>Project type</label>
                    <sui-dropdown selection :options="[
                        { text: 'Redirect', value: 'redirect' },
                        { text: 'Basic Website', value: 'basic' },
                        { text: 'PHP Website', value: 'php' },
                        { text: 'Laravel App', value: 'laravel' },
                    ]" v-model="tmpSite.type" />
                </sui-form-field>
            </sui-form-fields>

            <sui-form-field v-if="tmpSite.type == 'redirect'">
                <label>Destination</label>
                <sui-input v-model="tmpSite.redirect_to" placeholder="example.com" />
            </sui-form-field>
            <sui-form-fields inline v-if="tmpSite.type == 'redirect'">
                <label>Redirect Type</label>
                <sui-form-field>
                    <sui-checkbox radio v-model="tmpSite.redirect_type"
                        name="redirect" label="Temporary" value="301" />
                </sui-form-field>
                <sui-form-field>
                    <sui-checkbox radio v-model="tmpSite.redirect_type"
                        name="redirect" label="Permanent" value="302" />
                </sui-form-field>
            </sui-form-fields>

            <sui-form-field v-if="tmpSite.type && tmpSite.type != 'redirect'">
                <label>Clone URL</label>
                <sui-input v-model="tmpSite.source_repo" />
            </sui-form-field>
            <sui-form-field v-if="['basic', 'php', 'laravel'].includes(tmpSite.type)">
                <label>Document Root</label>
                <sui-input readonly v-model="tmpSite.document_root" />
            </sui-form-field>

            <sui-button-group>
                <router-link :to="{ name: 'apps' }" is="sui-button"
                    type="button" content="Cancel" />
                <sui-button-or />
                <sui-button type="submit" positive content="Save" />
            </sui-button-group>
        </sui-form>
    </sui-container>
</template>

<script>
export default {
    props: {
        site: {
            type: Object,
            default: () => ({}),
        },
    },
    computed: {
        tmpSite(){
            if (_.isEmpty(this.clonedSite) || this.site.id != this.clonedSite.id) {
                this.clonedSite = {...this.site};
            }

            return this.clonedSite;
        },
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
        setDocroot() {
            const site = this.clonedSite;
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
