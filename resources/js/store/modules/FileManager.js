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
        clearFile: state => {
            state.file = [];
        },
        replaceFile: (state, { oldFile, newFile }) => {
            const index = state.files.findIndex(f => f === oldFile);

            Vue.set(state.files, index, newFile);
        },
        removeFile: (state, file) => {
            const index = state.files.findIndex(f => f === file);

            state.files.splice(index, 1);
        },
        setPath: (state, path) => {
            state.currentPath = path;
        },
    },
    actions: {
        load: ({ commit, getters }, { path }) => {
            return new Promise((resolve, reject) => {
                axios.get('/api/files', {
                    params: { path },
                }).then(response => {
                    commit('setPath', path);
                    commit('setFiles', response.data);
                    resolve(response);
                }).catch(error => {
                    const data = getters.errorData(error);

                    commit('setPath', data.filepath);
                    commit('setFiles', data);
                    reject(error);
                });
            });
        },
        open: ({ commit, getters }, { file }) => {
            return new Promise((resolve, reject) => {
                commit('clearFile');

                axios.get('/api/files/', {
                    params: { file },
                }).then(response => {
                    commit('setPath', response.data.filepath);
                    commit('setFile', response.data);
                    resolve(response);
                }).catch(error => {
                    const data = getters.errorData(error);

                    commit('setFile', data);
                    reject(error);
                });
            });
        },
        create: ({ commit, state }, path) => {
            return new Promise((resolve, reject) => {
                axios.post(`/api/files?file=${path}`, {
                    contents: state.file.contents,
                }).then(response => {
                    commit('setPath', response.data.filepath);
                    commit('setFile', response.data);
                    resolve(response);
                }).catch(error => {
                    reject(error);
                });
            });
        },
        save: ({ commit, state }) => {
            return new Promise((resolve, reject) => {
                const fullpath = `${state.currentPath}/${state.file.filename}`;

                axios.put(`/api/files?file=${fullpath}`, {
                    contents: state.file.contents,
                }).then(response => {
                    commit('setFile', response.data);
                    resolve(response);
                }).catch(error => {
                    reject(error);
                });
            });
        },
        rename: ({ commit }, { file, newPath }) => {
            return new Promise((resolve, reject) => {
                axios.post('/api/files/rename', {
                    oldPath: `${file.filepath}/${file.filename}`,
                    newPath,
                }).then(response => {
                    commit('replaceFile', { oldFile: file, newFile: response.data });
                    resolve(response);
                }).catch(error => {
                    reject(error);
                });
            });
        },
        delete: ({ commit }, file) => {
            return new Promise((resolve, reject) => {
                const fullpath = `${file.filepath}/${file.filename}`;

                axios.delete(`/api/files?file=${fullpath}`).then(response => {
                    commit('removeFile', file);
                    resolve(response);
                }).catch(error => {
                    reject(error);
                });
            });
        },
    },
    getters: {
        all: state => state.files,
        file: state => state.file,
        currentPath: state => state.currentPath,
        errorData: () => error => {
            let data = error.response.data;

            if (!data.error || !data.error.code) {
                data = { error: {
                    code: error.response.status,
                    msg: data.message,
                }};
            }

            return data;
        },
    },
};
