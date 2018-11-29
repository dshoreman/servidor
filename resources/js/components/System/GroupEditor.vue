<template>
    <sui-form @submit.prevent="editMode ? updateGroup(tmpGroup.id_original) : addGroup()">
        <sui-form-fields>
            <sui-form-field width="ten">
                <label>Name</label>
                <input v-model="tmpGroup.name" ref="name" placeholder="group-name">
            </sui-form-field>
            <sui-form-field width="six">
                <label>GID</label>
                <input v-model="tmpGroup.id" type="number">
            </sui-form-field>
        </sui-form-fields>
        <sui-button-group fluid>
            <sui-button type="button" @click="reset()">Cancel</sui-button>
            <sui-button-or></sui-button-or>
            <sui-button type="submit" positive :content="editMode ? 'Update' : 'Create'" />
        </sui-button-group>

        <div v-if="tmpGroup.users.length">
            <sui-header size="small" v-show="editMode">Group Members</sui-header>
            <sui-list divided relaxed>
                <sui-list-item v-for="user in tmpGroup.users" :key="user">
                    <sui-list-icon name="user" size="large" vertical-align="middle" />
                    <sui-list-content>
                        <sui-list-header>{{ user }}</sui-list-header>
                    </sui-list-content>
                </sui-list-item>
            </sui-list>
        </div>

        <sui-header size="small" v-show="editMode">Danger Zone</sui-header>
        <sui-segment class="red" v-show="editMode">
            <sui-button negative icon="trash" type="button"
                content="Delete Group" @click="deleteGroup(tmpGroup.id)" />
        </sui-segment>
    </sui-form>
</template>

<script>
export default {
    data () {
        return {
            editMode: false,
            tmpGroup: {
                name: '',
                users: [],
            },
        };
    },
    created () {
        this.$root.$on('change-editor-group', (group) => {
            this.editMode = typeof group == 'object';

            if (!this.editMode) {
                group = {
                    id: null,
                    name: group,
                };
            }

            this.tmpGroup = Object.assign({}, group);
            this.tmpGroup.id_original = group.id;
            this.tmpGroup.users = [];

            this.$store.state.Group.editing = true;
            this.$nextTick(() => this.$refs.name.focus());
        });
    },
    methods: {
        addGroup () {
            if (this.tmpGroup.name.trim().length == 0) {
                return;
            }

            axios.post('/api/system/groups', this.tmpGroup).then(response => {
                this.$store.commit('createGroup', response.data)

                this.reset();
            });
        },
        updateGroup (id) {
            axios.put('/api/system/groups/'+id, this.tmpGroup).then(response => {
                this.$store.commit('updateGroup', {
                    gid: this.tmpGroup.id_original,
                    group: response.data
                });

                this.reset();
            });
        },
        deleteGroup (id) {
            axios.delete('/api/system/groups/'+id).then(response => {
                this.$store.commit('removeGroup', this.tmpGroup.id_original);

                this.reset();
            });
        },
        reset () {
            this.$store.state.Group.editing = false;
            this.editMode = false;

            this.tmpGroup = {
                id: null,
                id_original: null,
                name: '',
                users: [],
            };
        },
    }
}
</script>
