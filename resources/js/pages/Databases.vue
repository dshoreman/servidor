<template>
    <sui-grid container>
        <sui-grid-column>

            <sui-input placeholder="Type a name for your database..."
                    icon="search" class="fluid massive"
                    v-model="search" @keydown.enter="create"></sui-input>

            <sui-table selectable>
                <sui-table-row v-for="db, key in databases" :key="key">
                    <sui-table-cell>
                        <sui-icon color="violet" name="database" /> {{ db }}
                    </sui-table-cell>
                </sui-table-row>
            </sui-table>

        </sui-grid-column>
    </sui-grid>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    mounted () {
        this.$store.dispatch('databases/load');
    },
    computed: {
        ...mapGetters({
            databases: 'databases/filtered',
        }),
        search: {
            get () {
                return this.$store.state.databases.search;
            },
            set (value) {
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
