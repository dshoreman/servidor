<template>
    <sui-form @submit.prevent="editMode ? updateGroup(tmpGroup.gid_original) : createGroup()"
        :inverted="darkMode">
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

        <sui-form-field v-if="!editMode">
            <sui-segment :inverted="darkMode">
                <sui-checkbox toggle v-model="tmpGroup.system" value="1">
                    Create a system group
                </sui-checkbox>
            </sui-segment>
        </sui-form-field>

        <sui-form-field v-show="editMode">
            <label>Group Members</label>

            <sui-dropdown button type="button" class="icon"
                search :options="userDropdown" v-model="newUser"
                floating labeled text="Select User" />
            <sui-button basic positive circular type="button"
                icon="plus" @click="addUser"
                :disabled="newUser === null" />

            <sui-list divided>
                <sui-list-item v-if="!tmpGroup.users.length">
                    <sui-list-icon size="large" name="exclamation triangle" />
                    <sui-list-content>
                        <sui-list-header class="ui small">
                            There are currently no users in this group.
                        </sui-list-header>
                    </sui-list-content>
                </sui-list-item>
                <sui-list-item v-else v-for="user in tmpGroup.users" :key="user">
                    <sui-list-icon name="user" size="large" />
                    <sui-list-content>
                        <sui-button :icon="userIcon(user)" type="button" floated="right"
                                    :class="userClass(user)" @click="userToggle(user)" />
                        <sui-list-header class="ui small grey" v-if="deleted.includes(user)">
                            <strike>{{ user }}</strike>
                        </sui-list-header>
                        <sui-list-header v-else
                            :class="'ui small' + (hadUser(user) ? '' : ' green')">
                            {{ user }}
                        </sui-list-header>
                    </sui-list-content>
                </sui-list-item>
            </sui-list>
        </sui-form-field>

        <editor-buttons :editing="editMode" @cancel="reset()"
            @delete="deleteGroup(tmpGroup.gid)" />
    </sui-form>
</template>

<script>
import { mapGetters, mapState } from 'vuex';
import EditorButtons from './EditorButtons';

export default {
    components: {
        'editor-buttons': EditorButtons,
    },
    computed: {
        ...mapState({
            editing: state => state.systemGroups.editing,
            editMode: state => state.systemGroups.editMode,
            oldGroup: state => state.systemGroups.clean,
            tmpGroup: state => state.systemGroups.group,
        }),
        ...mapGetters({
            users: 'systemUsers/all',
            userDropdown: 'systemUsers/dropdown',
        }),
    },
    data() {
        return {
            deleted: [],
            newUser: null,
        };
    },
    watch: {
        editing(editing) {
            !editing || this.$nextTick(() => this.$refs.name.focus());
        },
    },
    methods: {
        createGroup() {
            if (0 === this.tmpGroup.name.trim().length) {
                return;
            }

            this.$store.dispatch('systemGroups/create', this.tmpGroup);
        },
        updateGroup(gid) {
            if (this.deleted.length) {
                this.deleted.forEach(user => {
                    const i = this.tmpGroup.users.indexOf(user);

                    this.tmpGroup.users.splice(i, 1);
                });
            }

            this.$store.dispatch('systemGroups/update', { gid, data: this.tmpGroup });
        },
        deleteGroup(gid) {
            this.$store.dispatch('systemGroups/delete', gid);
        },
        addUser() {
            const user = this.users[this.users.findIndex(
                u => u.uid === this.newUser,
            )];

            if (!this.tmpGroup.users.includes(user.name)) {
                this.tmpGroup.users.push(user.name);
            }

            this.newUser = null;
        },
        hadUser(name) {
            return this.oldGroup.users.includes(name);
        },
        deleteUser(name) {
            this.hadUser(name)
                ? this.deleted.push(name)
                : this.tmpGroup.users.splice(this.tmpGroup.users.indexOf(name), 1);
        },
        undeleteUser(name) {
            this.deleted.pop(this.deleted.indexOf(name));
        },
        userClass(user) {
            const colour = this.deleted.includes(user) ? 'grey' : 'red';

            return `mini ${colour} compact circular`;
        },
        userIcon(user) {
            return this.deleted.includes(user) ? 'undo' : 'minus';
        },
        userToggle(user) {
            return this.deleted.includes(user)
                ? this.undeleteUser(user)
                : this.deleteUser(user);
        },
        reset() {
            this.deleted = [];
            this.$store.commit('systemGroups/unsetEditorGroup');
        },
    },
};
</script>
