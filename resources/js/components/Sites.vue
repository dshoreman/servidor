<template>
    <sui-container class="grid">
        <sui-grid-row>
            <sui-grid-column>
                <sui-input placeholder="Type a name for your Application..."
                        icon="plus" class="fluid massive"
                        v-model="newSite" @keyup.enter="addSite"></sui-input>
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
    computed: {
        nextSiteId() {
            return this.sites.length + 1;
        }
    },
    data () {
        return {
            newSite: '',
            sites: [{
                'id': 1,
                'name': 'Test 1',
                'primary_domain': 'example.com',
            }, {
                'id': 2,
                'name': 'Test 2',
            }],
        };
    },
    methods: {
        addSite () {
            this.sites.push({
                id: this.nextSiteId,
                name: this.newSite,
            });
        }
    }
}
</script>
