<template>
    <sui-form @submit.prevent="editMode ? updateUser(tmpUser.id_original) : createUser()">
        <sui-form-fields>
            <sui-form-field width="ten">
                <label>Name</label>
                <input v-model="tmpUser.name" ref="name" placeholder="user-name">
            </sui-form-field>
            <sui-form-field width="six">
                <label>UID</label>
                <input v-model="tmpUser.id" type="number">
            </sui-form-field>
        </sui-form-fields>
        <sui-button-group fluid>
            <sui-button type="button" @click="reset()">Cancel</sui-button>
            <sui-button-or></sui-button-or>
            <sui-button type="submit" positive :content="editMode ? 'Update' : 'Create'" />
        </sui-button-group>

        <sui-header size="small" v-show="editMode">Danger Zone</sui-header>
        <sui-segment class="red" v-show="editMode">
            <sui-button negative icon="trash" type="button"
                content="Delete User" @click="deleteUser(tmpUser.id)" />
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
        updateUser (id) {
            this.$store.dispatch('updateUser', {id, data: this.tmpUser});
        },
        deleteUser (id) {
            this.$store.dispatch('deleteUser', id);
        },
        reset () {
            this.$store.commit('unsetEditorUser');
        },
    },
};
</script>
