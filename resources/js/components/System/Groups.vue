<template>
    <sui-grid>
        <sui-grid-column stretched :width="listWidth">
            <sui-segment attached>
                <sui-form @submit.prevent="edit(search)">
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
                        :group="group" :key="group.id" @edit="edit" />
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
                    <sui-button primary @click="edit">Add Group</sui-button>
                </div>
            </sui-segment>
        </sui-grid-column>

        <sui-grid-column :width="6" v-show="editing">
            <system-group-editor />
        </sui-grid-column>
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
            search: '',
            showSysGroups: false,
        };
    },
    mounted () {
        this.$store.dispatch('loadGroups');
    },
    computed: {
        editing () {
            return this.$store.state.Group.editing;
        },
        groups() {
            return this.$store.getters.groups;
        },
        filteredGroups() {
            return this.groups.filter(function (group) {
                if (!this.showSysGroups && group.id < 1000) {
                    return false;
                }

                return group.name.includes(this.search);
            }, this);
        },
        listWidth() {
            return this.editing ? 10 : 16;
        },
    },
    methods: {
        edit (group) {
            if (this.editing) {
                return;
            }

            this.$root.$emit('change-editor-group', group);
        },
    },
}
</script>
