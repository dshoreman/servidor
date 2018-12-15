<template lang="html">
    <sui-menu :widths="menu.length">
        <router-link :to="{ name: item.href }" is="sui-menu-item"
            v-for="(item, id) in menu" :key="id">
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
    }
}
</script>
