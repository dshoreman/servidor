<template>
    <sui-menu fluid vertical tabular>
        <router-link is="sui-menu-item"
            v-for="(item, id) in menu" :key="id"
            :content="item.name" :to="{ name: item.href }">
        </router-link>
    </sui-menu>
</template>

<script>
export default {
    data () {
        return {
            menu: [{
                name: 'Groups',
                href: 'system.groups'
            }, {
                name: 'Users',
                href: 'system.users'
            }],
        };
    }
}
</script>
