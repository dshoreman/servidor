<template>
    <sui-grid>
        <sui-grid-column stretched :width="listWidth">
            <sui-segment attached>
                <sui-form @submit.prevent="edit(search)">
                    <sui-form-field>
                        <sui-input placeholder="Search Groups..." class="huge fluid"
                            :value="search" @input="filterGroups" />
                    </sui-form-field>
                    <sui-form-field>
                        <sui-checkbox toggle label="Show system groups"
                            :inputValue="showSysGroups" @change="toggleSysGroups"/>
                    </sui-form-field>
                </sui-form>
            </sui-segment>

            <sui-segment attached v-if="filteredGroups.length">
                <sui-list divided relaxed>
                    <system-group-item v-for="group in filteredGroups"
                        :group="group" :key="group.gid" @edit="edit" />
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
import { mapState, mapGetters, mapMutations } from 'vuex';
import SystemGroupItem from './GroupItem';
import SystemGroupEditor from './GroupEditor';

export default {
    components: {
        SystemGroupItem,
        SystemGroupEditor,
    },
    mounted () {
        this.$store.dispatch('loadGroups');
    },
    computed: {
        ...mapState({
            editing: state => state.Group.editing,
            search: state => state.Group.currentFilter,
            showSysGroups: state => state.Group.showSystem,
        }),
        ...mapGetters([
            'groups',
            'filteredGroups',
        ]),
        listWidth() {
            return this.editing ? 10 : 16;
        },
    },
    methods: {
        ...mapMutations({
            edit: 'setEditorGroup',
            filterGroups: 'setFilter',
            toggleSysGroups: 'toggleSystemGroups',
        }),
    },
}
</script>
