<template>
    <sui-segment>
        <sui-form>
            <sui-form-field>
                <sui-input placeholder="Search Users..." class="huge fluid"
                    :value="search" @input="filterUsers" />
            </sui-form-field>
            <sui-form-field>
                <sui-checkbox toggle label="Show system users"
                    :inputValue="showSysUsers" @change="toggleSysUsers" />
            </sui-form-field>
        </sui-form>

        <sui-divider />

        <sui-list divided relaxed>
            <system-user-item v-for="user in filteredUsers"
                :user="user" :key="user.id" />
        </sui-list>
    </sui-segment>
</template>

<script>
import { mapState, mapGetters, mapMutations } from 'vuex';
import SystemUserItem from './UserItem';

export default {
    components: {
        SystemUserItem,
    },
    mounted () {
        this.$store.dispatch('loadUsers');
    },
    computed: {
        ...mapState({
            search: state => state.User.currentFilter,
            showSysUsers: state => state.User.showSystem,
        }),
        ...mapGetters([
            'users',
            'filteredUsers',
        ]),
    },
    methods: {
        ...mapMutations({
            filterUsers: 'setFilter',
            toggleSysUsers: 'toggleSystemUsers',
        }),
    },
};
</script>
