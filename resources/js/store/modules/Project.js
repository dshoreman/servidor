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
        create: ({ commit }, project) => {
            commit('addNewProject', project);
        },
    },
    getters: {
        all: state => state.projects,
    },
};
