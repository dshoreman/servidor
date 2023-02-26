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
        addProjectService: (state, service) => {
            const { project_id: pID } = service,
                project = { ...state.projects.find(p => p.id === pID) };

            project.services.push(service);

            Vue.set(state.projects, state.projects.findIndex(p => p.id === pID), project);
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
        createProject: async ({ commit }, project) => {
            try {
                const { data } = await axios.post('/api/projects', {
                    is_enabled: project.is_enabled,
                    name: project.name,
                });

                commit('addNewProject', data);

                return Promise.resolve(data);
            } catch (error) {
                return Promise.reject(error);
            }
        },
        createService: async ({ commit }, { projectId, service }) => {
            const { data } = await axios.post(`/api/projects/${projectId}/services`, service);

            commit('addProjectService', data);

            return data;
        },
        disable: ({ dispatch }, id) => dispatch('toggle', { id }),
        enable: ({ dispatch }, id) => dispatch('toggle', { id, enabled: true }),
        pull: ({ _ }, service) => axios.post(
            `/api/projects/${service.project.id}/services/${service.id}/pull`,
        ),
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
            p => p.services && 0 < p.services.length
                && path === p.services[0].document_root,
        ),
    },
};
