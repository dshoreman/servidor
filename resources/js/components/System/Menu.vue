<template>
    <sui-menu fluid vertical tabular>
        <a is="sui-menu-item"
            v-for="(item, id) in menu" :key="id"
            :content="item.name" :href="item.href">
        </a>
    </sui-menu>
</template>

<script>
export default {
    data () {
        return {
            menu: [{
                name: 'Groups',
                href: '/system/groups'
            }],
        };
    }
}
</script>
