<template>
    <sui-grid-column>

        <sui-input placeholder="Type a name for your database..."
                   icon="search" class="fluid massive"
                   :inverted="darkMode" :transparent="darkMode"
                   v-model="search" @keydown.enter="create"></sui-input>

        <sui-table compact selectable :inverted="darkMode">
            <sui-table-header>
                <sui-table-header-cell>Database Name</sui-table-header-cell>
                <sui-table-header-cell>Tables</sui-table-header-cell>
                <sui-table-header-cell>Character Set</sui-table-header-cell>
                <sui-table-header-cell>Default Collation</sui-table-header-cell>
            </sui-table-header>
            <sui-table-body>
                <sui-table-row v-for="(db, key) in databases" :key="key" @click="$router.push({
                    name: 'database',
                    params: { database: db.name }}
                )" style="cursor: pointer">
                    <sui-table-cell>
                        <sui-icon :color="darkMode ? 'orange' : 'violet'" name="database" />
                        {{ db.name }}
                    </sui-table-cell>
                    <sui-table-cell>
                        <span v-if="Number.isInteger(db.tableCount)">{{ db.tableCount }}</span>
                        <span v-else>unknown</span>
                    </sui-table-cell>
                    <sui-table-cell>{{ db.charset }}</sui-table-cell>
                    <sui-table-cell>{{ db.collation }}</sui-table-cell>
                </sui-table-row>
            </sui-table-body>
        </sui-table>

    </sui-grid-column>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    mounted() {
        this.$store.dispatch('databases/load');
    },
    computed: {
        ...mapGetters({
            databases: 'databases/filtered',
        }),
        search: {
            get() {
                return this.$store.state.databases.search;
            },
            set(value) {
                this.$store.dispatch('databases/filter', value);
            },
        },
    },
    methods: {
        ...mapActions({
            create: 'databases/create',
        }),
    },
};
</script>
