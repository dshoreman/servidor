<template lang="html">
    <sui-menu :widths="menu.length">
        <router-link :to="{ name: item.href }" is="sui-menu-item"
            v-for="(item, id) in menu" :key="id" :active="isActive(item)">
            {{ item.name }}
        </router-link>
    </sui-menu>
</template>

<script>
export default {
    data () {
        return {
            menu: [{
                name: 'Applications',
                href: 'apps'
            }, {
                name: 'Databases',
                href: 'databases'
            }, {
                name: 'Files',
                href: 'files'
            }, {
                name: 'System',
                href: 'system.groups'
            }],
        };
    },
    methods: {
        isActive (item) {
            let dest = item.href;

            if (dest.includes('.')) {
                dest = dest.substring(0, dest.indexOf('.'));
            }

            return this.$route.path.startsWith('/'+dest);
        },
    },
}
</script>
