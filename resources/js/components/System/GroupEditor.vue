<template>
    <sui-grid-column :width="6" v-show="showForm">
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
                <sui-button type="button" @click="closeEditor()">Cancel</sui-button>
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
    </sui-grid-column>
</template>

<script>
export default {
    props: {
        showForm: Boolean,
        tmpGroup: Object,
    },
    data () {
        return {
            editMode: false,
        };
    },
    created () {
        this.$root.$on('load-group-editor', (editing) => {
            this.editMode = editing;
            this.$refs.name.focus();
        });
    },
    methods: {
        closeEditor () {
            this.editMode = false;

            this.$emit('close');
        },
        addGroup () {
            if (this.tmpGroup.name.trim().length == 0) {
                return;
            }

            axios.post('/api/system/groups', this.tmpGroup).then(response => {
                this.$nextTick(() => this.$emit('created', response.data));
            });
        },
        updateGroup (id) {
            axios.put('/api/system/groups/'+id, this.tmpGroup).then(response => {
                this.$nextTick(() =>
                    this.$emit('updated', this.tmpGroup.id_original, response.data)
                );
            });
        },
        deleteGroup (id) {
            axios.delete('/api/system/groups/'+id).then(response => {
                this.$emit('deleted', this.tmpGroup.id_original);
            });
        },
    }
}
</script>
