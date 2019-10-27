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
        load: ({commit, state}) => {
            return new Promise((resolve, reject) =>
                axios.get('/api/databases').then(response => {
                    commit('setDatabases', response.data);
                    resolve(response);
                }).catch(error => reject(error))
            );
        },
        create: ({commit, state}) => {
            return new Promise((resolve, reject) =>
                axios.post('/api/databases', {
                    database: state.search,
                }).then(response => {
                    commit('addDatabase', response.data);
                    resolve(response);
                }).catch(error => reject(error))
            );
        },
        filter: ({commit, state}, value) => {
            commit('setFilter', value);
        },
    },
    getters: {
        all: state => {
            return state.databases;
        },
        filtered: state => {
            return state.databases.filter(db => {
                return db.toLowerCase().includes(state.search.toLowerCase());
            });
        },
    },
};
