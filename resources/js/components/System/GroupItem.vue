<template>
    <sui-list-item :active="active">
        <sui-list-icon name="users" size="large" vertical-align="middle" />
        <sui-list-content>
            <a is="sui-list-header" @click="edit">{{ group.name }}</a>
            <sui-list-description>
                <sui-list bulleted horizontal>
                    <span v-for="(user, index) in group.users"
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
    methods: {
        edit () {
            this.$emit('edit', this.group);
        }
    }
}
</script>
