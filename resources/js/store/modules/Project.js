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
        removeProject: (state, project) => {
            state.projects.splice(state.projects.findIndex(
                p => p.id === project.id,
            ), 1);
        },
        setProjects: (state, projects) => {
            state.projects = projects;
        },
        updateProject: (state, { id, project }) => {
            Vue.set(state.projects, state.projects.findIndex(p => p.id === id), project);
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
            const data = { ...project };

            if ('archive' === data.applications[0].template) {
                data.applications = [];
            }

            axios.post('/api/projects', data).then(response => {
                commit('addNewProject', response.data);
                resolve(response);
            }).catch(error => reject(error));
        }),
        disable: ({ dispatch }, id) => dispatch('toggle', { id }),
        enable: ({ dispatch }, id) => dispatch('toggle', { id, enabled: true }),
        pull: app => axios.post(`/api/projects/${app.project.id}/apps/${app.id}/pull`),
        remove: ({ commit }, id) => new Promise((resolve, reject) => {
            axios.delete(`/api/projects/${id}`).then(response => {
                commit('removeProject', id);
                resolve(response);
            }).catch(error => {
                commit('setErrors', {
                    message: error.message,
                    action: 'remove',
                });
                reject(error);
            });
        }),
        rename: ({ commit }, { id, name }) => new Promise((resolve, reject) => {
            axios.put(`/api/projects/${id}`, { name }).then(response => {
                commit('updateProject', { id, project: response.data });
                resolve(response);
            }).catch(error => reject(error));
        }),
        toggle: ({ commit }, { id, enabled = false }) => new Promise((resolve, reject) => {
            axios.put(`/api/projects/${id}`, { is_enabled: enabled }).then(response => {
                commit('updateProject', { id, project: response.data });
                resolve(response);
            }).catch(error => reject(error));
        }),
    },
    getters: {
        all: state => state.projects,
        find: state => id => state.projects.find(p => id === p.id),
        findByDocroot: state => path => state.projects.find(
            p => p.applications && 0 < p.applications.length
                && path === p.applications[0].document_root,
        ),
    },
};
