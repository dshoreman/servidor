export default {
    state: {
        users: [],
    },
    mutations: {
        setUsers: (state, users) => {
            state.users = users;
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
    },
};
