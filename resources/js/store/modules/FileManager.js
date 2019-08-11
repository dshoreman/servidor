export default {
    state: {
        files: [],
    },
    mutations: {
        setFiles: (state, files) => {
            state.files = files;
        },
    },
    actions: {
        loadFiles: ({commit}, {path}) => {
            return new Promise((resolve, reject) =>
                axios.get('/api/files', {
                    params: { path: path },
                }) .then(response => {
                    commit('setFiles', response.data);
                    resolve(response);
                }).catch(error => reject(error))
            );
        },
    },
    getters: {
        files: state => {
            return state.files;
        },
    },
}
