export default {
    namespaced: true,
    state: {
        clean: {},
        currentFilter: '',
        editing: false,
        editMode: false,
        showSystem: false,
        users: [],
        user: {
            name: '',
            groups: [],
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
            state.editMode = typeof user == 'object';

            if (!state.editMode) {
                user = {
                    uid: null,
                    gid: 0,
                    name: user,
                    groups: [],
                };
            }

            state.clean = Object.assign({}, user);
            state.clean.groups = user.groups ? user.groups.slice(0) : [];

            state.user = Object.assign({}, user);
            state.user.uid_original = user.uid;

            state.editing = true;
        },
        unsetEditorUser: (state) => {
            state.clean = {};
            state.editing = false;
            state.editMode = false;

            state.user = {
                uid: null,
                uid_original: null,
                name: '',
                gid: 0,
                groups: [],
            };
        },
        addUser: (state, user) => {
            if (!user.groups) {
                user.groups = [];
            }
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
        load: ({commit}) => {
            axios.get('/api/system/users').then(response => {
                commit('setUsers', response.data);
            });
        },
        edit: ({commit, state, getters}, user) => {
            // TODO: Add some kind of modal/confirm prompt in case
            //  the user wants to abort any changes and continue.
            if (state.editing && getters.userIsDirty) {
                return;
            }

            commit('setEditorUser', user);
        },
        create: ({commit}, user) => {
            axios.post('/api/system/users', user).then(response => {
                commit('addUser', response.data);
                commit('unsetEditorUser');
            });
        },
        update: ({commit}, {uid, user}) => {
            axios.put('/api/system/users/'+uid, user).then(response => {
                commit('updateUser', {
                    uid: uid,
                    user: response.data,
                });
                commit('unsetEditorUser');
            });
        },
        delete: ({commit, state}, uid) => {
            axios.delete('/api/system/users/'+uid).then(() => {
                commit('removeUser', state.user.uid_original);
                commit('unsetEditorUser');
            });
        },
    },
    getters: {
        all: state => {
            return state.users;
        },
        filtered: state => {
            return state.users.filter(user => {
                if (!state.showSystem && user.uid < 1000) {
                    return false;
                }

                return user.name.includes(state.currentFilter);
            });
        },
        dropdown: state => {
            return state.users.map(user => {
                return {
                    icon: 'user',
                    text: user.uid+' - '+user.name,
                    value: user.uid,
                };
            });
        },
        userIsDirty: state => {
            let old = state.clean,
                now = state.user;

            if (old === null) {
                return false;
            }

            return old.name != now.name
                || old.uid != now.uid
                || old.gid != now.gid
                || !_.isEqual(old.groups, now.groups);
        },
    },
};
