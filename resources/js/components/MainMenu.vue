<template>
    <sui-menu class="large" secondary fluid inverted vertical pointing v-if="use_new">
        <router-link :to="{ name: item.href }" is="sui-menu-item"
            v-for="(item, id) in menu" :key="id" :active="isActive(item)">
            {{ item.name }}
        </router-link>
    </sui-menu>
    <sui-menu :widths="menu.length" v-else>
        <router-link :to="{ name: item.href }" is="sui-menu-item"
            v-for="(item, id) in menu" :key="id" :active="isActive(item)">
            {{ item.name }}
        </router-link>
    </sui-menu>
</template>

<script>
export default {
    props: [
        'use_new',
    ],
    data () {
        return {
            menu: [{
                name: 'Applications',
                href: 'apps'
            }, {
                name: 'Databases',
                href: 'databases'
            }, {
                name: 'File Browser',
                href: 'files'
            }, {
                name: 'System',
                href: 'system.groups'
            }],
        };
    },
    methods: {
        isActive (item) {
            let href = '/' + item.href.split('.')[0];

            return this.$route.path.startsWith(href);
        },
    },
}
</script>
