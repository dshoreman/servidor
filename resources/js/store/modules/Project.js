export default {
    namespaced: true,
    state: {
        projects: [
        ],
    },
    mutations: {
        addNewProject: (state, project) => {
            state.projects.push(project);
        },
    },
    actions: {
        create: ({ commit }, project) => new Promise((resolve, reject) => {
            axios.post('/api/projects', project).then(response => {
                commit('addNewProject', response.data);
                resolve(response);
            }).catch(error => reject(error));
        }),
    },
    getters: {
        all: state => state.projects,
    },
};
