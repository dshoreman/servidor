const SYSTEM_GID_THRESHOLD = 1000;

export default {
    namespaced: true,
    state: {
        clean: {},
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
        setEditorGroup: (state, groupOrName) => {
            let group = groupOrName;

            state.editMode = 'object' === typeof group;

            if (!state.editMode) {
                group = {
                    gid: null,
                    name: group,
                    users: [],
                };
            }

            state.clean = { ...group };
            state.clean.users = [ ...group.users ];

            state.group = { ...group };
            state.group.users = [ ...group.users ];
            state.group.gid_original = group.gid;

            state.editing = true;
        },
        unsetEditorGroup: state => {
            state.clean = {};
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
        updateGroup: (state, { gid, group }) => {
            const index = state.groups.findIndex(g => g.gid === gid);

            Vue.set(state.groups, index, group);
        },
        removeGroup: (state, gid) => {
            const index = state.groups.findIndex(g => g.gid === gid);

            state.groups.splice(index, 1);
        },
    },
    actions: {
        load: ({ commit }) => {
            axios.get('/api/system/groups').then(response => {
                commit('setGroups', response.data);
            });
        },
        edit: ({ commit, state, getters }, group) => {
            if (state.editing && getters.groupIsDirty) {
                return;
            }

            commit('setEditorGroup', group);
        },
        create: ({ commit, state }) => {
            axios.post('/api/system/groups', state.group).then(response => {
                commit('addGroup', response.data);
                commit('unsetEditorGroup');
            });
        },
        update: ({ commit }, group) => {
            axios.put(`/api/system/groups/${group.gid}`, group.data).then(response => {
                commit('updateGroup', {
                    gid: group.gid,
                    group: response.data,
                });
                commit('unsetEditorGroup');
            });
        },
        delete: ({ commit, state }, gid) => {
            axios.delete(`/api/system/groups/${gid}`).then(() => {
                commit('removeGroup', state.group.gid_original);
                commit('unsetEditorGroup');
            });
        },
    },
    getters: {
        all: state => state.groups,
        filtered: state => state.groups.filter(group => {
            if (!state.showSystem && SYSTEM_GID_THRESHOLD > group.gid) {
                return false;
            }

            return group.name.includes(state.currentFilter);
        }),
        dropdown: state => state.groups.map(group => ({
            icon: 'users',
            text: `${group.gid} - ${group.name}`,
            value: group.gid,
        })),
        groupIsDirty: state => {
            const now = state.group,
                old = state.clean;

            if (null === old) {
                return false;
            }

            return old.name !== now.name
                || old.gid !== now.gid
                || !_.isEqual(old.users, now.users);
        },
    },
};
