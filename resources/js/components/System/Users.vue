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
            <system-user-item v-for="user in filteredUsers" :key="user.id" :user="user"
                v-if="user.username.includes(search) && (showSysUsers || user.id >= 1000)" />
        </sui-list>
    </sui-segment>
</template>

<script>
import SystemUserItem from './UserItem';

export default {
    components: {
        SystemUserItem,
    },
    data () {
        return {
            search: '',
            showSysUsers: false,
        };
    },
    mounted () {
        this.$store.dispatch('loadUsers');
    },
    computed: {
        ...mapGetters([
            'users',
        ]),
        filteredUsers () {
            return this.users.filter(user => user.username.includes(this.search));
        },
    },
};
</script>
