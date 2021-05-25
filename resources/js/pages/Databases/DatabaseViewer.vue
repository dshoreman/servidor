<template>
    <sui-grid-column>
        <sui-button id="levelup" icon="chevron left" @click="$router.push({
            name: 'databases'
        })" />
        <sui-breadcrumb size="massive">
            <sui-breadcrumb-section>
                {{ name }}
            </sui-breadcrumb-section>
        </sui-breadcrumb>
        <sui-table v-if="database && database.tables.length"
            :inverted="darkMode"
            selectable>
            <sui-table-header>
                <sui-table-header-cell>Table</sui-table-header-cell>
                <sui-table-header-cell collapsing>Rows</sui-table-header-cell>
                <sui-table-header-cell>Engine</sui-table-header-cell>
                <sui-table-header-cell>Collation</sui-table-header-cell>
                <sui-table-header-cell>Size</sui-table-header-cell>
            </sui-table-header>
            <sui-table-body>
                <sui-table-row v-for="table in database.tables" :key="table.name">
                    <sui-table-cell>{{ table.name }}</sui-table-cell>
                    <sui-table-cell collapsing>{{ table.rowCount }}</sui-table-cell>
                    <sui-table-cell>{{ table.engine }}</sui-table-cell>
                    <sui-table-cell>{{ table.collation }}</sui-table-cell>
                    <sui-table-cell>{{ table.size }}</sui-table-cell>
                </sui-table-row>
            </sui-table-body>
        </sui-table>
        <sui-segment v-else class="placeholder">
            <h3 is="sui-header" text-align="center" v-if="loading">
                Loading...
            </h3>
            <h3 is="sui-header" text-align="center" v-else>
                This database appears to be empty.
            </h3>
        </sui-segment>
    </sui-grid-column>
</template>

<script>
export default {
    async mounted() {
        this.loading = true;
        await this.$store.dispatch('databases/load');
        await this.$store.dispatch('databases/loadTables', this.name)
            .finally(() => {
                this.loading = false;
            });
    },
    props: {
        name: { type: String, default: '' },
    },
    data() {
        return {
            loading: false,
        };
    },
    computed: {
        database() {
            return this.$store.getters['databases/findByName'](this.name);
        },
    },
};
</script>
