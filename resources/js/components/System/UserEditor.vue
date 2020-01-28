<template>
    <sui-form @submit.prevent="editMode ? updateUser(tmpUser.uid_original) : createUser()"
        :inverted="darkMode">
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

        <sui-form-field v-if="editMode">
            <label>Default Shell</label>
            <input v-model="tmpUser.shell" type="text" placeholder="/bin/sh" />
        </sui-form-field>

        <sui-form-field>
            <label>Home Directory</label>
            <sui-segment class="homedir" :inverted="darkMode" v-if="editMode && tmpUser.dir">
                <router-link floated="right" is="sui-button" :to="{
                    name: 'files',
                    params: { path: tmpUser.dir }
                }" size="mini" basic compact icon="folder">Browse</router-link>
                <sui-input transparent v-model="tmpUser.dir" type="text" />
                <sui-checkbox v-model="tmpUser.move_home" value="1"
                    toggle v-if="tmpUser.dir != oldUser.dir">
                    Move the old directory
                </sui-checkbox>
            </sui-segment>
            <template v-else>
                <input type="text" v-model="tmpUser.dir" :placeholder="'/home/' + tmpUser.name" />
                <sui-segment :inverted="darkMode">
                    <sui-checkbox toggle v-model="tmpUser.create_home" value="1">
                        Create the home directory automatically
                    </sui-checkbox>
                </sui-segment>
            </template>
        </sui-form-field>

        <sui-form-field>
            <label>Primary Group</label>
            <sui-segment :inverted="darkMode" v-if="!editMode">
                <sui-checkbox toggle v-model="tmpUser.user_group" value="1">
                    Create and assign a group with the same name
                </sui-checkbox>
            </sui-segment>
            <sui-dropdown search selection v-if="editMode || !tmpUser.user_group"
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
                <sui-list-item v-if="!tmpUser.groups.length">
                    <sui-list-icon size="large" name="exclamation triangle" />
                    <sui-list-content>
                        <sui-list-header class="ui small">
                            {{ tmpUser.name }} is not a member of any other groups.
                        </sui-list-header>
                    </sui-list-content>
                </sui-list-item>
                <sui-list-item v-else v-for="group in tmpUser.groups" :key="group">
                    <sui-list-icon size="large" name="users" />
                    <sui-list-content>
                        <sui-button :icon="groupIcon(group)" type="button" floated="right"
                                    :class="groupClass(group)" @click="groupToggle(group)" />
                        <sui-list-header v-if="deleted.includes(group)" class="ui small grey">
                            <strike>{{ group }}</strike>
                        </sui-list-header>
                        <sui-list-header v-else
                            :class="'ui small' + (hadGroup(group) ? '' : ' green')">
                            {{ group }}
                        </sui-list-header>
                    </sui-list-content>
                </sui-list-item>
            </sui-list>
        </sui-form-field>

        <editor-buttons :editing="editMode" @cancel="reset()"
            @delete="deleteUser(tmpUser.uid)" />
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
            editing: state => state.systemUsers.editing,
            editMode: state => state.systemUsers.editMode,
            oldUser: state => state.systemUsers.clean,
            tmpUser: state => state.systemUsers.user,
        }),
        ...mapGetters({
            groups: 'systemGroups/all',
            groupDropdown: 'systemGroups/dropdown',
        }),
    },
    data() {
        return {
            deleted: [],
            newGroup: null,
        };
    },
    watch: {
        editing(editing) {
            !editing || this.$nextTick(() => this.$refs.name.focus());
        },
    },
    methods: {
        createUser() {
            if (0 === this.tmpUser.name.trim().length) {
                return;
            }

            this.$store.dispatch('systemUsers/create', this.tmpUser);
        },
        updateUser(uid) {
            if (this.deleted.length) {
                this.deleted.forEach(group => {
                    const i = this.tmpUser.groups.indexOf(group);

                    this.tmpUser.groups.splice(i, 1);
                });
            }

            this.$store.dispatch('systemUsers/update', { uid, user: this.tmpUser });
        },
        deleteUser(uid) {
            this.$store.dispatch('systemUsers/delete', uid);
        },
        addGroup() {
            const group = this.groups[this.groups.findIndex(
                g => g.gid === this.newGroup,
            )];

            if (!this.tmpUser.groups.includes(group.name)) {
                this.tmpUser.groups.push(group.name);
            }

            this.newGroup = null;
        },
        hadGroup(name) {
            return this.oldUser.groups.includes(name);
        },
        deleteGroup(name) {
            this.hadGroup(name)
                ? this.deleted.push(name)
                : this.tmpUser.groups.splice(this.tmpUser.groups.indexOf(name), 1);
        },
        undeleteGroup(name) {
            this.deleted.pop(this.deleted.indexOf(name));
        },
        groupClass(group) {
            const colour = this.deleted.includes(group) ? 'grey' : 'red';

            return `mini ${colour} compact circular`;
        },
        groupToggle(group) {
            return this.deleted.includes(group)
                ? this.undeleteGroup(group)
                : this.deleteGroup(group);
        },
        groupIcon(group) {
            return this.deleted.includes(group) ? 'undo' : 'minus';
        },
        reset() {
            this.deleted = [];
            this.$store.commit('systemUsers/unsetEditorUser');
        },
    },
};
</script>
