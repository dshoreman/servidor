const SYSTEM_UID_THRESHOLD = 1000;

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
            gid: '100',
            groups: [],
            create_home: true,
            user_group: true,
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
        setEditorUser: (state, userOrName) => {
            let user = userOrName;

            state.editMode = 'object' === typeof user;

            if (state.editMode) {
                user.move_home = true;
            } else {
                user = {
                    uid: null,
                    gid: '100',
                    name: user,
                    groups: [],
                    shell: '/bin/zsh',
                    create_home: true,
                    user_group: true,
                };
            }

            state.clean = Object.assign({}, user);
            state.clean.groups = user.groups ? user.groups.slice(0) : [];

            state.user = Object.assign({}, user);
            state.user.uid_original = user.uid;

            state.editing = true;
        },
        unsetEditorUser: state => {
            state.clean = {};
            state.editing = false;
            state.editMode = false;

            state.user = {
                uid: null,
                uid_original: null,
                name: '',
                gid: '',
                groups: [],
            };
        },
        addUser: (state, user) => {
            if (!user.groups) {
                user.groups = [];
            }
            if (Number.isInteger(user.gid)) {
                user.gid = user.gid.toString();
            }
            state.users.push(user);
        },
        updateUser: (state, { uid, user }) => {
            const index = state.users.findIndex(u => u.uid === uid);

            Vue.set(state.users, index, user);
        },
        removeUser: (state, uid) => {
            const index = state.users.findIndex(u => u.uid === uid);

            state.users.splice(index, 1);
        },
    },
    actions: {
        load: ({ commit }) => {
            axios.get('/api/system/users').then(response => {
                commit('setUsers', response.data);
            });
        },
        edit: ({ commit, state, getters }, user) => {
            /* eslint-disable no-warning-comments */
            // TODO: Add some kind of modal/confirm prompt in case
            //  the user wants to abort any changes and continue.
            if (state.editing && getters.userIsDirty) {
                return;
            }

            commit('setEditorUser', user);
        },
        create: ({ commit, dispatch }, user) => {
            axios.post('/api/system/users', user).then(response => {
                commit('addUser', response.data);
                dispatch('systemGroups/load', null, { root: true }).then(() => {
                    commit('unsetEditorUser');
                });
            });
        },
        update: ({ commit }, { uid, user }) => {
            axios.put(`/api/system/users/${uid}`, user).then(response => {
                commit('updateUser', {
                    uid,
                    user: response.data,
                });
                commit('unsetEditorUser');
            });
        },
        delete: ({ commit, state }, { uid, purge = false }) => {
            axios.delete(`/api/system/users/${uid}`, {
                data: { deleteHome: purge },
            }).then(() => {
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
                if (!state.showSystem && SYSTEM_UID_THRESHOLD > user.uid) {
                    return false;
                }

                return user.name.includes(state.currentFilter);
            });
        },
        dropdown: state => {
            return state.users.map(user => {
                return {
                    icon: 'user',
                    text: `${user.uid} - ${user.name}`,
                    value: user.uid,
                };
            });
        },
        userIsDirty: state => {
            const now = state.user,
                old = state.clean;

            if (null === old) {
                return false;
            }

            return old.name !== now.name
                || old.uid !== now.uid
                || old.gid !== now.gid
                || !_.isEqual(old.groups, now.groups);
        },
    },
};
