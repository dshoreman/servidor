<template>
    <sui-form @submit.prevent="editMode ? updateGroup(tmpGroup.gid_original) : createGroup()">
        <sui-form-fields>
            <sui-form-field width="ten">
                <label>Name</label>
                <input v-model="tmpGroup.name" ref="name" placeholder="group-name">
            </sui-form-field>
            <sui-form-field width="six">
                <label>GID</label>
                <input v-model="tmpGroup.gid" type="number">
            </sui-form-field>
        </sui-form-fields>

        <sui-form-field v-show="editMode">
            <label>Group Members</label>

            <sui-dropdown button type="button" class="icon"
                search :options="userDropdown" v-model="newUser"
                floating labeled text="Select User" />
            <sui-button basic positive circular type="button"
                icon="plus" @click="addUser"
                :disabled="newUser === null" />

            <sui-list divided>
                <sui-list-item v-for="user in tmpGroup.users" :key="user">
                    <sui-list-icon name="user" size="large" />

                    <sui-list-content v-if="!deleted.includes(user)">
                        <sui-button icon="minus" type="button" @click="deleteUser(user)"
                            floated="right" class="circular compact red mini" />
                        <sui-list-header :class="(hadUser(user) ? '' : 'green ') + 'ui small'">
                            {{ user }}
                        </sui-list-header>
                    </sui-list-content>

                    <sui-list-content v-if="deleted.includes(user)">
                        <sui-button icon="undo" type="button" @click="undeleteUser(user)"
                            floated="right" class="circular compact grey mini" />
                        <sui-list-header class="ui small grey">
                            <strike>{{ user }}</strike>
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
                content="Delete Group" @click="deleteGroup(tmpGroup.gid)" />
        </sui-segment>
    </sui-form>
</template>

<script>
import { mapState, mapGetters } from 'vuex';

export default {
    computed: {
        ...mapState({
            editing: state => state.Group.editing,
            editMode: state => state.Group.editMode,
            oldGroup: state => state.Group.clean,
            tmpGroup: state => state.Group.group,
        }),
        ...mapGetters([
            'users',
            'userDropdown',
        ]),
    },
    data () {
        return {
            deleted: [],
            newUser: null,
        };
    },
    watch: {
        editing (editing) {
            (!editing) || this.$nextTick(() => this.$refs.name.focus());
        },
    },
    methods: {
        createGroup () {
            if (this.tmpGroup.name.trim().length == 0) {
                return;
            }

            this.$store.dispatch('createGroup', this.tmpGroup);
        },
        updateGroup (gid) {
            if (this.deleted.length) {
                this.deleted.forEach(user => {
                    let i = this.tmpGroup.users.indexOf(user);

                    this.tmpGroup.users.splice(i, 1);
                });
            }

            this.$store.dispatch('updateGroup', {gid, data: this.tmpGroup});
        },
        deleteGroup (gid) {
            this.$store.dispatch('deleteGroup', gid);
        },
        addUser () {
            let user = this.users[this.users.findIndex(
                u => u.uid == this.newUser
            )];

            if (!this.tmpGroup.users.includes(user.name)) {
                this.tmpGroup.users.push(user.name);
            }

            this.newUser = null;
        },
        hadUser (name) {
            return this.oldGroup.users.includes(name);
        },
        deleteUser (name) {
            this.hadUser(name)
             ? this.deleted.push(name)
             : this.tmpGroup.users.splice(this.tmpGroup.users.indexOf(name), 1);
        },
        undeleteUser (name) {
            this.deleted.pop(this.deleted.indexOf(name));
        },
        reset () {
            this.deleted = [];
            this.$store.commit('unsetEditorGroup');
        },
    },
};
</script>
