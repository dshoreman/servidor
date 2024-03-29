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
        setTables: (state, { name, tables }) => {
            const database = state.databases.find(db => db.name === name),
                index = state.databases.findIndex(db => db.name === name);

            database.tables = tables;

            Vue.set(state.databases, index, database);
        },
        addDatabase: (state, database) => {
            state.databases.push(database.database);
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
        loadTables: ({ commit }, database) => new Promise((resolve, reject) => {
            axios.get(`/api/databases/${database}`).then(response => {
                const { name, tables } = response.data;

                commit('setTables', { name, tables });
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
            db => db.name.toLowerCase().includes(state.search.toLowerCase()),
        ),
        findByName: state => database => state.databases.find(db => db.name === database),
    },
};
