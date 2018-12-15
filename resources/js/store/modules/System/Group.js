export default {
    state: {
        currentFilter: '',
        groups: [],
        group: {
            name: '',
            users: [],
        },
        editing: false,
        editMode: false,
        showSystem: false,
    },
    mutations: {
        setGroups: (state, groups) => {
            state.groups = groups;
        },
        setFilter: (state, value) => {
            state.currentFilter = value;
        },
        toggleSystemGroups: (state, value) => {
            state.showSystem = value;
        },
        setEditorGroup: (state, group) => {
            if (state.editing) {
                return;
            }

            state.editMode = typeof group == 'object';

            if (!state.editMode) {
                group = {
                    gid: null,
                    name: group,
                    users: [],
                };
            }

            state.group = Object.assign({}, group);
            state.group.gid_original = group.gid;

            state.editing = true;
        },
        unsetEditorGroup: (state) => {
            state.editing = false;
            state.editMode = false;

            state.group = {
                gid: null,
                name: '',
                users: [],
                gid_original: null,
            };
        },
        addGroup: (state, group) => {
            state.groups.push(group);
        },
        updateGroup: (state, {gid, group}) => {
            let index = state.groups.findIndex(g => g.gid === gid);

            Vue.set(state.groups, index, group);
        },
        removeGroup: (state, gid) => {
            let index = state.groups.findIndex(g => g.gid === gid);

            state.groups.splice(index, 1);
        },
    },
    actions: {
        loadGroups: ({commit}) => {
            axios.get('/api/system/groups').then(response => {
                commit('setGroups', response.data);
            });
        },
        createGroup: ({commit, state}) => {
            axios.post('/api/system/groups', state.group).then(response => {
                commit('addGroup', response.data);
                commit('unsetEditorGroup');
            });
        },
        updateGroup: ({commit}, group) => {
            axios.put('/api/system/groups/'+group.gid, group.data).then(response => {
                commit('updateGroup', {
                    gid: group.gid,
                    group: response.data
                });
                commit('unsetEditorGroup');
            });
        },
        deleteGroup: ({commit, state}, gid) => {
            axios.delete('/api/system/groups/'+gid).then(response => {
                commit('removeGroup', state.group.gid_original);
                commit('unsetEditorGroup');
            });
        },
    },
    getters: {
        groups: state => {
            return state.groups;
        },
        groupDropdown: state => {
            return state.groups.map(group => {
                return {
                    icon: 'users',
                    text: group.gid+' - '+group.name,
                    value: group.gid,
                };
            });
        },
        filteredGroups: state => {
            return state.groups.filter(group => {
                if (!state.showSystem && group.gid < 1000) {
                    return false;
                }

                return group.name.includes(state.currentFilter);
            });
        }
    },
};
