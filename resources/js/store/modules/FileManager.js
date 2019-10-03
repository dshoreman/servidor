export default {
    namespaced: true,
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
        load: ({commit}, {path}) => {
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
        open: ({commit}, {file}) => {
            return new Promise((resolve, reject) =>
                axios.get('/api/files/', {
                    params: { file: file }
                }).then(response => {
                    commit('setFile', response.data);
                    resolve(response);
                }).catch(error => {
                    let data = error.response.data;

                    if (!data.error || !data.error.code) {
                        data = { error: {
                            code: error.response.status,
                            msg: data.message
                        }};
                    }

                    commit('setFile', data);
                    reject(error);
                })
            );
        },
    },
    getters: {
        all: state => state.files,
        file: state => state.file,
        currentPath: state => state.currentPath,
    },
}
