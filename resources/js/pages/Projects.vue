<template>
    <sui-grid container>
        <sui-grid-column>
            <router-view :projects="projects" />
        </sui-grid-column>
    </sui-grid>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
    computed: {
        ...mapGetters({
            projects: 'projects/all',
        }),
    },
};
</script>
