export default {
    namespaced: true,
    state: {
        databases: [],
        search: '',
    },
    mutations: {
        setDatabases: (state, databases) => {
            state.databases = databases;
        },
        addDatabase: (state, database) => {
            state.databases.push(database);
        },
        setFilter: (state, value) => {
            state.search = value;
        },
    },
    actions: {
        load: ({ commit }) => new Promise((resolve, reject) => {
            axios.get('/api/databases').then(response => {
                commit('setDatabases', response.data);
                resolve(response);
            }).catch(error => reject(error));
        }),
        create: ({ commit, state }) => new Promise((resolve, reject) => {
            axios.post('/api/databases', {
                database: state.search,
            }).then(response => {
                commit('addDatabase', response.data);
                resolve(response);
            }).catch(error => reject(error));
        }),
        filter: ({ commit }, value) => {
            commit('setFilter', value);
        },
    },
    getters: {
        all: state => state.databases,
        filtered: state => state.databases.filter(
            db => db.toLowerCase().includes(state.search.toLowerCase()),
        ),
    },
};
