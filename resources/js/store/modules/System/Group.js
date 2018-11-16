export default {
    state: {
        groups: [],
    },
    mutations: {
        setGroups: (state, groups) => {
            state.groups = groups;
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
