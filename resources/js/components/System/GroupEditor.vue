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
            <sui-list divided>
                <sui-list-item v-for="user in tmpGroup.users" :key="user">
                    <sui-list-icon name="user" size="large" />
                    <sui-list-content>
                        <sui-list-header>{{ user }}</sui-list-header>
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
            tmpGroup: state => state.Group.group,
        }),
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
            this.$store.dispatch('updateGroup', {gid, data: this.tmpGroup});
        },
        deleteGroup (gid) {
            this.$store.dispatch('deleteGroup', gid);
        },
        reset () {
            this.$store.commit('unsetEditorGroup');
        },
    },
};
</script>
