<template>
    <sui-grid container>
        <sui-grid-column class="grouplist" :width="listWidth">
            <sui-segment attached>
                <sui-form @submit.prevent="edit(search)" :inverted="darkMode">
                    <sui-form-field>
                        <sui-input placeholder="Search Groups..." class="huge fluid"
                            :inverted="darkMode" :transparent="darkMode"
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
                        :group="group" :key="group.gid" :active="group.gid === activeGroup" @edit="edit" />
                </sui-list>
            </sui-segment>

            <sui-segment attached :inverted="darkMode" class="placeholder" v-else>
                <sui-header icon>
                    <sui-icon name="search" />
                    We couldn't find any groups matching your search
                    <sui-header-subheader v-if="!showSysGroups">
                        Are you looking for a system group?
                    </sui-header-subheader>
                </sui-header>
                <div class="inline">
                    <sui-button @click="filterGroups('')">Clear Search</sui-button>
                    <sui-button primary @click="edit(search)">Add Group</sui-button>
                </div>
            </sui-segment>
        </sui-grid-column>

        <sui-grid-column :width="6" v-show="editing">
            <system-group-editor />
        </sui-grid-column>
    </sui-grid>
</template>

<script>
import { mapState, mapGetters, mapActions, mapMutations } from 'vuex';
import SystemGroupItem from './GroupItem';
import SystemGroupEditor from './GroupEditor';

export default {
    components: {
        SystemGroupItem,
        SystemGroupEditor,
    },
    mounted () {
        this.$store.dispatch('systemGroups/load');
        this.$store.dispatch('systemUsers/load');
    },
    computed: {
        ...mapState({
            editing: state => state.systemGroups.editing,
            search: state => state.systemGroups.currentFilter,
            showSysGroups: state => state.systemGroups.showSystem,
            activeGroup: state => state.systemGroups.clean.gid,
        }),
        ...mapGetters({
            groups: 'systemGroups/all',
            filteredGroups: 'systemGroups/filtered',
        }),
        listWidth() {
            return this.editing ? 10 : 16;
        },
    },
    methods: {
        ...mapMutations({
            filterGroups: 'systemGroups/setFilter',
            toggleSysGroups: 'systemGroups/toggleSystemGroups',
        }),
        ...mapActions({
            edit: 'systemGroups/edit',
        }),
    },
}
</script>
