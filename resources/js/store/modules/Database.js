export default {
    namespaced: true,
    state: {
        databases: [],
    },
    mutations: {
        setDatabases: (state, databases) => {
            state.databases = databases;
        },
    },
    actions: {
        load: ({commit, state}) => {
            return new Promise((resolve, reject) =>
                axios.get('/api/databases') .then(response => {
                    commit('setDatabases', response.data);
                    resolve(response);
                }).catch(error => reject(error))
            );
        },
    },
    getters: {
        all: state => {
            return state.databases;
        },
    },
};
