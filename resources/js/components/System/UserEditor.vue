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

        <sui-form-field v-show="editMode">
            <label>Secondary Groups</label>

            <sui-dropdown button type="button" class="icon"
                search :options="groupDropdown" v-model="newGroup"
                floating labeled text="Select Group" />
            <sui-button basic positive circular type="button"
                icon="plus" @click="addGroup"
                :disabled="newGroup === null" />

            <sui-list divided>
                <sui-list-item v-for="group in tmpUser.groups" :key="group">
                    <sui-list-icon size="large" name="users" />

                    <sui-list-content v-if="!deleted.includes(group)">
                        <sui-button icon="minus" type="button" @click="deleteGroup(group)"
                            floated="right" class="circular compact red mini" />
                        <sui-list-header class="ui small">{{ group }}</sui-list-header>
                    </sui-list-content>

                    <sui-list-content v-if="deleted.includes(group)">
                        <sui-button icon="undo" type="button" @click="undeleteGroup(group)"
                            floated="right" class="circular compact grey mini" />
                        <sui-list-header class="ui small grey">
                            <strike>{{ group }}</strike>
                        </sui-list-header>
                    </sui-list-content>
                </sui-list-item>
            </sui-list>
        </sui-form-field>

        <sui-button-group fluid>
            <sui-button type="button" @click="reset()">Cancel</sui-button>
            <sui-button-or></sui-button-or>
            <sui-button type="submit" positive :content="editMode ? 'Update' : 'Create'" />
        </sui-button-group>

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
            'groups',
            'groupDropdown',
        ]),
    },
    data () {
        return {
            deleted: [],
            newGroup: null,
        };
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
            if (this.deleted.length) {
                this.deleted.forEach(group => {
                    let i = this.tmpUser.groups.indexOf(group);

                    this.tmpUser.groups.splice(i, 1);
                });
            }

            this.$store.dispatch('updateUser', {uid, user: this.tmpUser});
        },
        deleteUser (uid) {
            this.$store.dispatch('deleteUser', uid);
        },
        addGroup () {
            let group = this.groups[this.groups.findIndex(
                g => g.gid == this.newGroup
            )];

            if (!this.tmpUser.groups.includes(group.name)) {
                this.tmpUser.groups.push(group.name);
            }

            this.newGroup = null;
        },
        deleteGroup (name) {
            this.deleted.push(name);
        },
        undeleteGroup (name) {
            this.deleted.pop(this.deleted.indexOf(name));
        },
        reset () {
            this.deleted = [];
            this.$store.commit('unsetEditorUser');
        },
    },
};
</script>
