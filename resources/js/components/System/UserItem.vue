<template>
    <sui-list-item :active="active">
        <sui-list-icon name="user" size="large" vertical-align="middle" />
        <sui-list-content>
            <a is="sui-list-header" @click="edit">{{ name }}</a>
            <sui-list-description>&nbsp;</sui-list-description>
        </sui-list-content>
    </sui-list-item>
</template>

<script>
export default {
    props: {
        user: Object,
        active: Boolean,
    },
    data () {
        return {
            name: this.user.name,
        };
    },
    methods: {
        edit () {
            this.$emit('edit', this.user);
        },
    },
};
</script>
