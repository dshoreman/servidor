<template>
    <sui-list-item>
        <sui-list-icon name="user" size="large" vertical-align="middle"></sui-list-icon>
        <sui-list-content>
            <a is="sui-list-header" @click="edit">{{ name }}</a>
        </sui-list-content>
    </sui-list-item>
</template>

<script>
export default {
    props: {
        user: Object,
    },
    data () {
        return {
            'name': this.user.name,
        };
    },
    methods: {
        edit () {
            this.$emit('edit', this.user);
        },
    },
};
</script>
