<template>
    <sui-menu class="large" secondary fluid inverted vertical pointing>
        <router-link :to="{ name: item.href }" is="sui-menu-item" v-if="!item.menu"
            v-for="item in menu" :key="item.href" :active="isActive(item)">
            {{ item.name }}
        </router-link>

        <template v-else>
            <sui-menu-item class="link" :active="isActive(item)">

                <sui-menu-header :content="item.name" />

                <sui-menu secondary fluid inverted vertical pointing>
                    <router-link :to="{ name: subitem.href }" is="sui-menu-item" :active="isActive(subitem, true)"
                        v-for="subitem in item.menu" :key="subitem.href" class="submenu-item">
                        {{ subitem.name }}
                    </router-link>
                </sui-menu>

            </sui-menu-item>
        </template>
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
                name: 'File Browser',
                href: 'files'
            }, {
                name: 'System',
                href: 'system.groups',
                menu: [{
                    name: 'Groups',
                    href: 'system.groups',
                }, {
                    name: 'Users',
                    href: 'system.users',
                }],
            }],
        };
    },
    methods: {
        isActive (item, absolute = false) {
            if (absolute) {
                return this.$route.path === '/' + item.href.replace('.', '/');
            }

            let href = '/' + item.href.split('.')[0];

            return this.$route.path.startsWith(href);
        },
    },
};
</script>
