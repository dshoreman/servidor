<template>
    <sui-breadcrumb class="massive">
        <template v-for="(segment, index) in pathParts">

            <sui-breadcrumb-section link @click="$emit('cd', segment.path)"
                v-if="segment.path != path">
                {{ segment.dirname }}
            </sui-breadcrumb-section>
            <sui-breadcrumb-section v-else>
                {{ segment.dirname }}
            </sui-breadcrumb-section>

            <sui-breadcrumb-divider @click="$emit('cd', segment.path)"
                v-if="index < (pathParts.length - 1)" />

        </template>
    </sui-breadcrumb>
</template>

<script>
export default {
    props: {
        path: {
            type: String,
            default: '',
        },
    },
    computed: {
        pathParts: function() {
            let parts = [],
                path = '';

            for (let part of this.path.split('/')) {
                path = path + part + '/';

                parts.push({
                    'path': path.replace(/\/+$/, ''),
                    'dirname': part,
                });
            }

            return parts;
        },
    },
}
</script>
