export default {
    state: {
        currentPath: '',
        files: [],
        file: [],
    },
    mutations: {
        setFiles: (state, files) => {
            state.files = files;
        },
        setFile: (state, file) => {
            state.file = file;
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
        openFile: ({commit}, {file}) => {
            return new Promise((resolve, reject) =>
                axios.get('/api/files/' + file).then(response => {
                    commit('setFile', response.data);
                    resolve(response);
                }).catch(error => {
                    commit('setFile', { contents: error.response.data.message })
                    reject(error);
                })
            );
        },
    },
    getters: {
        files: state => state.files,
        file: state => state.file,
        currentPath: state => state.currentPath,
    },
}
