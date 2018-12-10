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
                    id: null,
                    name: group,
                    users: [],
                };
            }

            state.group = Object.assign({}, group);
            state.group.id_original = group.id;

            state.editing = true;
        },
        unsetEditorGroup: (state) => {
            state.editing = false;
            state.editMode = false;

            state.group = {
                id: null,
                name: '',
                users: [],
                id_original: null,
            };
        },
        addGroup: (state, group) => {
            state.groups.push(group);
        },
        updateGroup: (state, {gid, group}) => {
            let index = state.groups.findIndex(g => g.id === gid);

            Vue.set(state.groups, index, group);
        },
        removeGroup: (state, gid) => {
            let index = state.groups.findIndex(g => g.id === gid);

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
            axios.put('/api/system/groups/'+group.id, group.data).then(response => {
                commit('updateGroup', {
                    gid: group.id,
                    group: response.data
                });
                commit('unsetEditorGroup');
            });
        },
        deleteGroup: ({commit, state}, id) => {
            axios.delete('/api/system/groups/'+id).then(response => {
                commit('removeGroup', state.group.id_original);
                commit('unsetEditorGroup');
            });
        },
    },
    getters: {
        groups: state => {
            return state.groups;
        },
        filteredGroups: state => {
            return state.groups.filter(group => {
                if (!state.showSystem && group.id < 1000) {
                    return false;
                }

                return group.name.includes(state.currentFilter);
            });
        }
    },
};
