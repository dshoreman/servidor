export default {
    state: {
        groups: [],
        group: {
            name: '',
            users: [],
        },
        editing: false,
        editMode: false,
    },
    mutations: {
        setGroups: (state, groups) => {
            state.groups = groups;
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
                };
            }

            state.group = Object.assign({}, group);
            state.group.id_original = group.id;
            state.group.users = [];

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
        createGroup: (state, group) => {
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
    },
    getters: {
        groups: state => {
            return state.groups;
        },
    },
};
