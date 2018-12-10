export default {
    state: {
        currentFilter: '',
        showSystem: false,
        users: [],
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
    },
    actions: {
        loadUsers: ({commit}) => {
            axios.get('/api/system/users').then(response => {
                commit('setUsers', response.data);
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

                return user.username.includes(state.currentFilter);
            });
        },
    },
};
