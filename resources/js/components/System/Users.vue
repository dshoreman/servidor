<template>
    <sui-segment>
        <sui-form>
            <sui-form-field>
                <sui-input placeholder="Search Users..." class="huge fluid" v-model="search" />
            </sui-form-field>
            <sui-form-field>
                <sui-checkbox toggle label="Show system users"
                    v-model="showSysUsers" />
            </sui-form-field>
        </sui-form>

        <sui-divider />

        <sui-list divided relaxed>
            <sui-list-item v-for="user in filteredUsers" :key="user.id"
                v-if="user.username.includes(search) && (showSysUsers || user.id >= 1000)">
                <sui-list-icon name="users" size="large" vertical-align="middle"></sui-list-icon>
                <sui-list-content>
                    <a is="sui-list-header">{{ user.username }}</a>
                </sui-list-content>
            </sui-list-item>
        </sui-list>
    </sui-segment>
</template>

<script>
export default {
    data () {
        return {
            users: [],
            search: '',
            showSysUsers: true,
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
