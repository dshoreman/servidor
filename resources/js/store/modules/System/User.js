export default {
    state: {
        currentFilter: '',
        editing: false,
        editMode: false,
        showSystem: false,
        users: [],
        user: {
            name: '',
        },
    },
    mutations: {
        setUsers: (state, users) => {
            state.users = users;
        },
        setFilter: (state, value) => {
            state.currentFilter = value;
        },
        toggleSystemUsers: (state, value) => {
            state.showSystem = value;
        },
        setEditorUser: (state, user) => {
            if (state.editing) {
                return;
            }

            state.editMode = typeof user == 'object';

            if (!state.editMode) {
                user = {
                    id: null,
                    name: user,
                };
            }

            state.user = Object.assign({}, user);
            state.user.id_original = user.id;

            state.editing = true;
        },
        unsetEditorUser: (state) => {
            state.editing = false;
            state.editMode = false;

            state.user = {
                id: null,
                id_original: null,
                name: '',
            };
        },
        addUser: (state, user) => {
            state.users.push(user);
        },
        updateUser: (state, {uid, user}) => {
            let index = state.users.findIndex(u => u.id === uid);

            Vue.set(state.users, index, user);
        },
        removeUser: (state, uid) => {
            let index = state.users.findIndex(u => u.id === uid);

            state.users.splice(index, 1);
        },
    },
    actions: {
        loadUsers: ({commit}) => {
            axios.get('/api/system/users').then(response => {
                commit('setUsers', response.data);
            });
        },
        createUser: ({commit, state}) => {
            axios.post('/api/system/users', state.user).then(response => {
                commit('addUser', response.data);
                commit('unsetEditorUser');
            });
        },
        updateUser: ({commit}, user) => {
            axios.put('/api/system/users/'+user.id, user.data).then(response => {
                commit('updateUser', {
                    uid: user.id,
                    user: response.data,
                });
                commit('unsetEditorUser');
            });
        },
        deleteUser: ({commit, state}, id) => {
            axios.delete('/api/system/users/'+id).then(response => {
                commit('removeUser', state.user.id_original);
                commit('unsetEditorUser');
            });
        },
    },
    getters: {
        users: state => {
            return state.users;
        },
        filteredUsers: state => {
            return state.users.filter(user => {
                if (!state.showSystem && user.id < 1000) {
                    return false;
                }

                return user.name.includes(state.currentFilter);
            });
        },
    },
};
