<template>
    <sui-menu class="large" secondary fluid inverted vertical pointing>
        <template v-for="item in menu">
            <router-link :to="{ name: item.href }" is="sui-menu-item"
                v-if="!item.menu" :active="isActive(item)" :key="item.href">
                {{ item.name }}
            </router-link>

            <sui-menu-item class="link" :active="isActive(item)" :key="item.href" v-else>

                <sui-menu-header :content="item.name" />

                <sui-menu secondary fluid inverted vertical pointing>
                    <router-link :to="{ name: subitem.href }" is="sui-menu-item"
                        class="submenu-item" :active="isActive(subitem, true)"
                        v-for="subitem in item.menu" :key="subitem.href">
                        {{ subitem.name }}
                    </router-link>
                </sui-menu>

            </sui-menu-item>
        </template>
    </sui-menu>
</template>

<script>
export default {
    data() {
        return {
            menu: [{
                name: 'Applications',
                href: 'apps',
            }, {
                name: 'Databases',
                href: 'databases',
            }, {
                name: 'File Browser',
                href: 'files',
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
        isActive(item, absolute = false) {
            if (absolute) {
                return this.$route.path === `/${item.href.replace('.', '/')}`;
            }

            const href = `/${item.href.split('.')[0]}`;

            return this.$route.path.startsWith(href);
        },
    },
};
</script>
