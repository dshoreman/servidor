export default {
    namespaced: true,
    state: {
            databases: [
                { name: 'foo' },
                { name: 'bar' },
                { name: 'baz' },
            ],
    },
    mutations: {},
    actions: {},
    getters: {
        all: state => {
            return state.databases;
        },
    },
};
