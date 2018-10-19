<template>
    <sui-list divided relaxed>
        <sui-list-item v-for="group in groups" :key="group.id">
            <sui-list-icon name="users" size="large" vertical-align="middle"></sui-list-icon>
            <sui-list-content>
                <a is="sui-list-header">{{ group.name }}</a>
            </sui-list-content>
        </sui-list-item>
    </sui-list>
</template>

<script>
export default {
    data () {
        return {
            groups: [],
        };
    },
    mounted () {
        this.fetchGroups();
    },
    methods: {
        fetchGroups() {
            axios.get('/api/system/groups').then(response => {
                this.groups = response.data;
            });
        },
    },
}
</script>
