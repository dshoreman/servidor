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
        addProjectApp: (state, app) => {
            const { project_id: pID } = app,
                project = { ...state.projects.find(p => p.id === pID) };

            project.applications.push(app);

            Vue.set(state.projects, state.projects.findIndex(p => p.id === pID), project);
        },
        addProjectRedirect: (state, redirect) => {
            const { project_id: pID } = redirect,
                project = { ...state.projects.find(p => p.id === pID) };

            project.redirects.push(redirect);

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
        createApp: async ({ commit }, { projectId, app }) => {
            const { data } = await axios.post(`/api/projects/${projectId}/apps`, app);

            commit('addProjectApp', data);

            return data;
        },
        createRedirect: async ({ commit }, { projectId, redirect }) => {
            const { data } = await axios.post(`/api/projects/${projectId}/redirects`, redirect);

            commit('addProjectRedirect', data);

            return data;
        },
        disable: ({ dispatch }, id) => dispatch('toggle', { id }),
        enable: ({ dispatch }, id) => dispatch('toggle', { id, enabled: true }),
        pull: ({ _ }, app) => axios.post(`/api/projects/${app.project.id}/apps/${app.id}/pull`),
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
