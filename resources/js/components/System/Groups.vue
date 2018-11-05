<template>
    <sui-grid>
        <sui-grid-column stretched :width="showForm ? 10 : 16">
            <sui-segment attached>
                <sui-form @submit.prevent="createFromSearch()">
                    <sui-form-field>
                        <sui-input placeholder="Search Groups..." class="huge fluid"
                            v-model="search" />
                    </sui-form-field>
                    <sui-form-field>
                        <sui-checkbox toggle label="Show system groups"
                            v-model="showSysGroups" />
                    </sui-form-field>
                </sui-form>
            </sui-segment>

            <sui-segment attached v-if="filteredGroups.length">
                <sui-list divided relaxed>
                    <sui-list-item v-for="group in filteredGroups" :key="group.id">
                        <sui-list-icon name="users" size="large" vertical-align="middle"></sui-list-icon>
                        <sui-list-content>
                            <a is="sui-list-header" @click="openEditor(group)">{{ group.name }}</a>
                            <sui-list-description>
                                <sui-list bulleted horizontal>
                                    <span v-for="(user, id) in group.users"
                                        :key="id" is="sui-list-item">
                                        {{ user }}
                                    </span>
                                </sui-list>
                            </sui-list-description>
                        </sui-list-content>
                    </sui-list-item>
                </sui-list>
            </sui-segment>

            <sui-segment attached class="placeholder" v-else>
                <sui-header icon>
                    <sui-icon name="search" />
                    We couldn't find any groups matching your search
                    <sui-header-subheader v-if="!showSysGroups">
                        Are you looking for a system group?
                    </sui-header-subheader>
                </sui-header>
                <div class="inline">
                    <sui-button @click="search = ''">Clear Search</sui-button>
                    <sui-button primary @click="showForm = true">Add Group</sui-button>
                </div>
            </sui-segment>
        </sui-grid-column>

        <sui-grid-column :width="6" v-show="showForm">
            <sui-form @submit.prevent="editMode ? updateGroup(tmpGroup.id) : addGroup()">
                <sui-form-field>
                    <label>Name</label>
                    <input v-model="tmpGroup.name" ref="name" placeholder="group-name">
                </sui-form-field>
                <sui-button-group fluid>
                    <sui-button type="button" @click="cancelEdit()">Cancel</sui-button>
                    <sui-button-or></sui-button-or>
                    <sui-button type="submit" positive :content="editMode ? 'Update' : 'Create'" />
                </sui-button-group>

                <sui-header size="small" v-show="editMode">Danger Zone</sui-header>
                <sui-segment class="red" v-show="editMode">
                    <sui-button negative icon="trash" type="button"
                        content="Delete Group" @click="deleteGroup(tmpGroup.id)" />
                </sui-segment>
            </sui-form>
        </sui-grid-column>
    </sui-grid>
</template>

<script>
export default {
    data () {
        return {
            groups: [],
            search: '',
            showSysGroups: false,
            showForm: false,
            editMode: false,
            tmpGroup: {
                name: '',
                users: '',
            },
        };
    },
    mounted () {
        this.fetchGroups();
    },
    computed: {
        filteredGroups() {
            return this.groups.filter(function (group) {
                if (!this.showSysGroups && group.id < 1000) {
                    return false;
                }

                return group.name.includes(this.search);
            }, this);
        },
    },
    methods: {
        fetchGroups() {
            axios.get('/api/system/groups').then(response => {
                this.groups = response.data;
            });
        },
        createFromSearch () {
            if (this.tmpGroup.name.trim().length) {
                return;
            }

            this.tmpGroup.name = this.search;
            this.editMode = false;
            this.showForm = true;

            this.$nextTick(() => this.$refs.name.focus());
        },
        addGroup () {
            if (this.tmpGroup.name.trim().length == 0) {
                return;
            }

            axios.post('/api/system/groups', this.tmpGroup).then(response => {
                response.data.id = 9001;
                this.groups.push(response.data);

                cancelEdit();
            });
        },
        updateGroup (id) {
            axios.put('/api/system/groups/'+id, this.tmpGroup).then(response => {
                cancelEdit();
            });
        },
        deleteGroup (id) {
            axios.delete('/api/system/groups/'+id).then(response => {
                let index = this.groups.indexOf(this.tmpGroup);
                this.groups.splice(index, 1);

                cancelEdit();
            });
        },
        openEditor (group) {
            this.tmpGroup = group;
            this.editMode = true;
            this.showForm = true;
        },
        cancelEdit () {
            this.showForm = false;
            this.editMode = false;

            this.tmpGroup.id = 0;
            this.tmpGroup.name = '';
            this.tmpGroup.users = '';
        }
    },
}
</script>
