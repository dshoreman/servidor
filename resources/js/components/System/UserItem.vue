<template>
    <sui-list-item>
        <sui-list-icon name="users" size="large" vertical-align="middle"></sui-list-icon>
        <sui-list-content>
            <a is="sui-list-header">{{ user.username }}</a>
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
            'username': this.user.username,
        };
    },
};
</script>
