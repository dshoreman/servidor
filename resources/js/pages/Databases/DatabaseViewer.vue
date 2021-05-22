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
            :columns="2"
            selectable>
            <sui-table-header>
                <sui-table-header-cell>Table</sui-table-header-cell>
            </sui-table-header>
        </sui-table>
        <sui-segment v-else class="placeholder">
            <h3 is="sui-header" text-align="center">
                This database appears to be empty.
            </h3>
        </sui-segment>
    </sui-grid-column>
</template>

<script>
export default {
    props: {
        name: { type: String, default: '' },
    },
};
</script>
