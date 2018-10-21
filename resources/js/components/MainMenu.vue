<template lang="html">
    <sui-menu :widths="menu.length">
        <sui-menu-item link :href="item.href" v-for="(item, id) in menu" :key="id">
            {{ item.name }}
        </sui-menu-item>
    </sui-menu>
</template>

<script>
export default {
    data () {
        return {
            menu: [{
                name: 'Applications',
                href: '/apps'
            }, {
                name: 'Databases',
                href: '/databases'
            }, {
                name: 'Files',
                href: '/files'
            }, {
                name: 'System',
                href: '/system/groups'
            }],
        };
    }
}
</script>
