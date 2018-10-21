<template>
<div>
    <sui-input placeholder="Search Users..." class="huge fluid" v-model="search" />

    <sui-list divided relaxed>
        <sui-list-item v-for="user in filteredUsers" :key="user.id"
            v-if="user.username.includes(search)">
            <sui-list-icon name="users" size="large" vertical-align="middle"></sui-list-icon>
            <sui-list-content>
                <a is="sui-list-header">{{ user.username }}</a>
            </sui-list-content>
        </sui-list-item>
    </sui-list>
</div>
</template>

<script>
export default {
    data () {
        return {
            users: [],
            search: '',
        };
    },
    mounted () {
        this.fetchUsers();
    },
    computed: {
        filteredUsers () {
            return this.users.filter(user => user.username.includes(this.search));
        },
    },
    methods: {
        fetchUsers () {
            axios.get('/api/system/users').then(response => {
                this.users = response.data;
            });
        },
    },
}
</script>
