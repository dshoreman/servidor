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
                    <system-group-item v-for="group in filteredGroups"
                        :group="group" :key="group.id"
                        @edit="openEditor" />
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

        <system-group-editor :showForm="showForm" :tmpGroup="tmpGroup"
            @created="addGroup" @updated="updated" @close="cancelEdit" />
    </sui-grid>
</template>

<script>
import SystemGroupItem from './GroupItem';
import SystemGroupEditor from './GroupEditor';

export default {
    components: {
        SystemGroupItem,
        SystemGroupEditor,
    },
    data () {
        return {
            groups: [],
            search: '',
            showSysGroups: false,
            showForm: false,
            tmpGroup: {
                name: '',
                users: [],
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
            this.showForm = true;

            this.$nextTick(() => this.$root.$emit('load-group-editor', false));
        },
        addGroup (group) {
            this.groups.push(group);

            this.cancelEdit();
        },
        updated (group, old_id) {
            let index = this.groups.findIndex(
                g => g.id === old_id
            );

            Vue.set(this.groups, index, group);

            this.cancelEdit();
        },
        openEditor (group) {
            this.tmpGroup = Object.assign({}, group);
            this.tmpGroup.id_original = group.id;

            this.$root.$emit('load-group-editor', true);
            this.showForm = true;
        },
        cancelEdit () {
            this.showForm = false;

            this.tmpGroup.id = null;
            this.tmpGroup.id_original = null;
            this.tmpGroup.name = '';
            this.tmpGroup.users = [];
        }
    },
}
</script>
