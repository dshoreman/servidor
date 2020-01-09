<template>
    <sui-grid container>
        <sui-grid-column class="userlist" :width="listWidth">
            <sui-segment attached>
                <sui-form @submit.prevent="edit(search)" :inverted="darkMode">
                    <sui-form-field>
                        <sui-input placeholder="Search Users..." class="huge fluid"
                            :inverted="darkMode" :transparent="darkMode"
                            :value="search" @input="filterUsers" />
                    </sui-form-field>
                    <sui-form-field>
                        <sui-checkbox toggle label="Show system users"
                            :inputValue="showSysUsers" @change="toggleSysUsers" />
                    </sui-form-field>
                </sui-form>
            </sui-segment>

            <sui-segment attached v-if="filteredUsers.length">
                <sui-list divided relaxed>
                    <system-user-item v-for="user in filteredUsers"
                        :user="user" :key="user.uid" :active="user.uid === activeUser" @edit="edit" />
                </sui-list>
            </sui-segment>

            <sui-segment attached :inverted="darkMode" class="placeholder" v-else>
                <sui-header icon>
                    <sui-icon name="search" />
                    We couldn't find any users matching your search
                    <sui-header-subheader v-if="!showSysUsers">
                        Are you looking for a system user?
                    </sui-header-subheader>
                </sui-header>
                <div class="inline">
                    <sui-button @click="filterUsers('')">Clear Search</sui-button>
                    <sui-button primary @click="edit(search)">Add User</sui-button>
                </div>
            </sui-segment>
        </sui-grid-column>

        <sui-grid-column :width="6" v-show="editing">
            <system-user-editor />
        </sui-grid-column>
    </sui-grid>
</template>

<script>
import { mapActions, mapGetters, mapMutations, mapState } from 'vuex';
import SystemUserEditor from './UserEditor';
import SystemUserItem from './UserItem';

export default {
    components: {
        SystemUserItem,
        SystemUserEditor,
    },
    mounted () {
        this.$store.dispatch('systemUsers/load');
        this.$store.dispatch('systemGroups/load');
    },
    computed: {
        ...mapState({
            editing: state => state.systemUsers.editing,
            search: state => state.systemUsers.currentFilter,
            showSysUsers: state => state.systemUsers.showSystem,
            activeUser: state => state.systemUsers.clean.uid,
        }),
        ...mapGetters({
            users: 'systemUsers/all',
            filteredUsers: 'systemUsers/filtered',
        }),
        listWidth() {
            return this.editing ? 10 : 16;
        },
    },
    methods: {
        ...mapActions({
            edit: 'systemUsers/edit',
        }),
        ...mapMutations({
            filterUsers: 'systemUsers/setFilter',
            toggleSysUsers: 'systemUsers/toggleSystemUsers',
        }),
    },
};
</script>
