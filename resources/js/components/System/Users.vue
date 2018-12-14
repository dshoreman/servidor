<template>
    <sui-grid>
        <sui-grid-column stretched :width="listWidth">
            <sui-segment attached>
                <sui-form @submit.prevent="edit(search)">
                    <sui-form-field>
                        <sui-input placeholder="Search Users..." class="huge fluid"
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
                        :user="user" :key="user.uid" @edit="edit" />
                </sui-list>
            </sui-segment>

            <sui-segment attached class="placeholder" v-else>
                <sui-header icon>
                    <sui-icon name="search" />
                    We couldn't find any users matching your search
                    <sui-header-subheader v-if="!showSysUsers">
                        Are you looking for a system user?
                    </sui-header-subheader>
                </sui-header>
                <div class="inline">
                    <sui-button @click="search = ''">Clear Search</sui-button>
                    <sui-button primary @click="edit">Add User</sui-button>
                </div>
            </sui-segment>
        </sui-grid-column>

        <sui-grid-column :width="6" v-show="editing">
            <system-user-editor />
        </sui-grid-column>
    </sui-grid>
</template>

<script>
import { mapState, mapGetters, mapMutations } from 'vuex';
import SystemUserItem from './UserItem';
import SystemUserEditor from './UserEditor';

export default {
    components: {
        SystemUserItem,
        SystemUserEditor,
    },
    mounted () {
        this.$store.dispatch('loadUsers');
    },
    computed: {
        ...mapState({
            editing: state => state.User.editing,
            search: state => state.User.currentFilter,
            showSysUsers: state => state.User.showSystem,
        }),
        ...mapGetters([
            'users',
            'filteredUsers',
        ]),
        listWidth() {
            return this.editing ? 10 : 16;
        },
    },
    methods: {
        ...mapMutations({
            edit: 'setEditorUser',
            filterUsers: 'setFilter',
            toggleSysUsers: 'toggleSystemUsers',
        }),
    },
};
</script>
