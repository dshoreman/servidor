<template>
    <sui-grid container>
        <sui-grid-column>
            <sui-table selectable>
                <sui-table-row v-for="db in databases" :key="db.name">
                    <sui-table-cell>
                        <sui-icon color="violet" name="database" /> {{ db.name }}
                    </sui-table-cell>
                </sui-table-row>
            </sui-table>
        </sui-grid-column>
    </sui-grid>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
    computed: {
        ...mapGetters({
            databases: 'databases/all',
        }),
    },
};
</script>
