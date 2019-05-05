<template>
    <sui-container>
        <h2 is="sui-header">
            Editing {{ site.name }}
            <sui-header-subheader>
                {{ site.primary_domain }}
            </sui-header-subheader>
        </h2>

        <sui-form>
            <sui-form-field>
                <label>App Name</label>
                <sui-input v-model="site.name" placeholder="My Blog" />
            </sui-form-field>
            <sui-form-field>
                <label>Primary Domain</label>
                <sui-input v-model="site.primary_domain" placeholder="example.com" />
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
}
</script>
