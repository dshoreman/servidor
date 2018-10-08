<template>
    <sui-container class="grid">
        <sui-grid-row>
            <sui-grid-column>
                <sui-input placeholder="Type a name for your Application..."
                        icon="plus" class="fluid massive"></sui-input>
            </sui-grid-column>
        </sui-grid-row>
        <sui-card-group>
            <sui-card v-for="site in sites" :key="site.id">
                <sui-card-content>
                    <sui-card-header>{{ site.name }}</sui-card-header>
                    <sui-card-meta>{{ site.primary_domain }}</sui-card-meta>
                </sui-card-content>
                <sui-button attached="bottom">
                    <sui-icon name="cogs"></sui-icon> Manage Site
                </sui-button>
            </sui-card>
        </sui-card-group>
    </sui-container>
</template>

<script>
export default {
    data () {
        return {
            sites: [{
                'id': 1,
                'name': 'Test 1',
                'primary_domain': 'example.com',
            }, {
                'id': 2,
                'name': 'Test 2',
            }],
        };
    }
}
</script>
