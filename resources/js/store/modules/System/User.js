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
                    uid: null,
                    name: user,
                };
            }

            state.user = Object.assign({}, user);
            state.user.uid_original = user.uid;

            state.editing = true;
        },
        unsetEditorUser: (state) => {
            state.editing = false;
            state.editMode = false;

            state.user = {
                uid: null,
                uid_original: null,
                name: '',
            };
        },
        addUser: (state, user) => {
            state.users.push(user);
        },
        updateUser: (state, {uid, user}) => {
            let index = state.users.findIndex(u => u.uid === uid);

            Vue.set(state.users, index, user);
        },
        removeUser: (state, uid) => {
            let index = state.users.findIndex(u => u.uid === uid);

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
        updateUser: ({commit}, {uid, user}) => {
            axios.put('/api/system/users/'+uid, user).then(response => {
                commit('updateUser', {
                    uid: uid,
                    user: response.data,
                });
                commit('unsetEditorUser');
            });
        },
        deleteUser: ({commit, state}, uid) => {
            axios.delete('/api/system/users/'+uid).then(response => {
                commit('removeUser', state.user.uid_original);
                commit('unsetEditorUser');
            });
        },
    },
    getters: {
        users: state => {
            return state.users;
        },
        userDropdown: state => {
            return state.users.map(user => {
                return {
                    icon: 'user',
                    text: user.uid+' - '+user.name,
                    value: user.uid,
                };
            });
        },
        filteredUsers: state => {
            return state.users.filter(user => {
                if (!state.showSystem && user.uid < 1000) {
                    return false;
                }

                return user.name.includes(state.currentFilter);
            });
        },
    },
};
