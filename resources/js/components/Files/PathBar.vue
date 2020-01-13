<template>
    <h2>
        <sui-button id="levelup" :icon="upIcon" @click="goUp()" />

        <sui-breadcrumb class="massive">
            <template v-for="(segment, index) in pathParts">

                <sui-breadcrumb-section v-if="segment.path != path"
                    link :key="index" @click="goTo(segment.path)">
                    {{ segment.dirname }}
                </sui-breadcrumb-section>
                <sui-breadcrumb-section :key="index" v-else>
                    {{ segment.dirname }}
                </sui-breadcrumb-section>

                <sui-breadcrumb-divider @click="goTo(segment.path)"
                    v-if="index < (pathParts.length - 1)" :key="'divider' + index" />

            </template>
        </sui-breadcrumb>

        <slot />
    </h2>
</template>

<script>
export default {
    props: {
        upIcon: {
            type: String,
            default: 'level up',
        },
        path: {
            type: String,
            default: '',
        },
    },
    computed: {
        pathParts() {
            const parts = [];
            let path = '';

            for (const part of this.path.split('/')) {
                path = `${path + part}/`;

                parts.push({
                    path: path.replace(/\/+$/u, ''),
                    dirname: part,
                });
            }

            return parts;
        },
    },
    methods: {
        goTo(path) {
            this.$router.push({
                name: 'files',
                params: { path: path ? path : '/' },
            });
        },
        goUp() {
            this.goTo(this.path.substr(0, this.path.lastIndexOf('/')));
        },
    },
};
</script>
