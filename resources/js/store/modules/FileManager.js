export default {
    state: {
        currentPath: '',
        files: [],
    },
    mutations: {
        setFiles: (state, files) => {
            state.files = files;
        },
        setPath: (state, path) => {
            state.currentPath = path;
        }
    },
    actions: {
        loadFiles: ({commit}, {path}) => {
            return new Promise((resolve, reject) =>
                axios.get('/api/files', {
                    params: { path: path },
                }) .then(response => {
                    commit('setPath', path);
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
        currentPath: state => {
            return state.currentPath;
        }
    },
}
