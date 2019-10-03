<template>
    <sui-list-item :active="active">
        <sui-list-icon name="users" size="large" vertical-align="middle" />
        <sui-list-content>
            <a is="sui-list-header" @click="edit">{{ name }}</a>
            <sui-list-description>
                <sui-list bulleted horizontal>
                    <span v-for="(user, index) in users"
                        :key="index" is="sui-list-item">
                        {{ user }}
                    </span>
                </sui-list>
            </sui-list-description>
        </sui-list-content>
    </sui-list-item>
</template>

<script>
export default {
    props: {
        group: Object,
        active: Boolean,
    },
    data () {
        return {
            'name': this.group.name,
            'users': this.group.users,
        };
    },
    methods: {
        edit () {
            this.$emit('edit', this.group);
        }
    }
}
</script>
