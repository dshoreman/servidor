<template>
    <sui-form @submit.prevent="editMode ? updateUser(tmpUser.uid_original) : createUser()">
        <sui-form-fields>
            <sui-form-field width="ten">
                <label>Name</label>
                <input v-model="tmpUser.name" ref="name" placeholder="user-name">
            </sui-form-field>
            <sui-form-field width="six">
                <label>UID</label>
                <input v-model="tmpUser.uid" type="number">
            </sui-form-field>
        </sui-form-fields>
        <sui-form-field>
            <label>Primary Group</label>
            <sui-dropdown search selection
                :options="groupDropdown" v-model="tmpUser.gid" />
        </sui-form-field>

        <sui-button-group fluid>
            <sui-button type="button" @click="reset()">Cancel</sui-button>
            <sui-button-or></sui-button-or>
            <sui-button type="submit" positive :content="editMode ? 'Update' : 'Create'" />
        </sui-button-group>

        <sui-header size="small" v-show="editMode">Secondary Groups</sui-header>
        <sui-list divided relaxed v-show="editMode">
            <sui-list-item v-for="group in tmpUser.groups" :key="group">
                <sui-list-icon name="users" size="large" vertical-align="middle" />
                <sui-list-content>
                    <sui-list-header>{{ group }}</sui-list-header>
                </sui-list-content>
            </sui-list-item>
        </sui-list>

        <sui-header size="small" v-show="editMode">Danger Zone</sui-header>
        <sui-segment class="red" v-show="editMode">
            <sui-button negative icon="trash" type="button"
                content="Delete User" @click="deleteUser(tmpUser.uid)" />
        </sui-segment>
    </sui-form>
</template>

<script>
import { mapState, mapGetters } from 'vuex';

export default {
    computed: {
        ...mapState({
            editing: state => state.User.editing,
            editMode: state => state.User.editMode,
            tmpUser: state => state.User.user,
        }),
        ...mapGetters([
            'groupDropdown',
        ]),
    },
    watch: {
        editing (editing) {
            (!editing) || this.$nextTick(() => this.$refs.name.focus());
        },
    },
    methods: {
        createUser () {
            if (this.tmpUser.name.trim().length == 0) {
                return;
            }

            this.$store.dispatch('createUser', this.tmpUser);
        },
        updateUser (uid) {
            this.$store.dispatch('updateUser', {uid, user: this.tmpUser});
        },
        deleteUser (uid) {
            this.$store.dispatch('deleteUser', uid);
        },
        reset () {
            this.$store.commit('unsetEditorUser');
        },
    },
};
</script>
