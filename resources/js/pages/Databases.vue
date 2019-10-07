<template>
    <sui-grid container>
        <sui-grid-column>
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
import { mapGetters } from 'vuex';

export default {
    mounted () {
        this.$store.dispatch('databases/load');
    },
    computed: {
        ...mapGetters({
            databases: 'databases/all',
        }),
    },
};
</script>
