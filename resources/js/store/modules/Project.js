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
        setProjects: (state, projects) => {
            state.projects = projects;
        },
    },
    actions: {
        load: ({ commit }) => new Promise((resolve, reject) => {
            axios.get('/api/projects').then(response => {
                commit('setProjects', response.data);
                resolve(response);
            }).catch(error => reject(error));
        }),
        create: ({ commit }, project) => new Promise((resolve, reject) => {
            axios.post('/api/projects', project).then(response => {
                commit('addNewProject', response.data);
                resolve(response);
            }).catch(error => reject(error));
        }),
        pull: app => axios.post(`/api/projects/${app.project.id}/apps/${app.id}/pull`),
    },
    getters: {
        all: state => state.projects,
        find: state => id => state.projects.find(p => id === p.id),
    },
};
