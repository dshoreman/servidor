const HTTP_UNPROCESSABLE_ENTITY = 422;

export default {
    namespaced: true,
    state: {
        alerts: [],
        current: {},
        currentFilter: '',
        errors: [],
        sites: [],
        site: {
            name: '',
            create_site: false,
        },
    },
    mutations: {
        setSuccess: (state, message) => {
            state.alerts.push({
                title: 'Success!',
                message,
                isSuccess: true,
            });
        },
        setErrors: (state, { message, errors, action = 'save' }) => {
            state.alerts.push({
                title: `Could not ${action} Site!`,
                message,
                isSuccess: false,
            });

            if (errors) {
                state.errors = errors;
            }
        },
        clearMessages: state => {
            state.alerts = [];
            state.errors = [];
        },
        setFilter: (state, value) => {
            state.currentFilter = value;
        },
        setSites: (state, sites) => {
            state.sites = sites;
        },
        setEditorSite: (state, id) => {
            const site = { ...state.sites.find(s => s.id === id) };

            // Shitty hack because SemanticUI-Vue doesn't support a simple
            // goddamn Number value for its sui-checkbox component. Wtf?!
            if (site.redirect_type) {
                site.redirect_type = site.redirect_type.toString();
            }

            state.current = site;
        },
        addSite: (state, site) => {
            state.sites.push(site);
            state.site.name = '';
        },
        updateSite: (state, { id, site }) => {
            const index = state.sites.findIndex(s => s.id === id);

            Vue.set(state.sites, index, site);

            state.current = {
                ...state.sites.find(s => s.id === id),
            };
        },
    },
    actions: {
        load: ({ commit }) => new Promise((resolve, reject) => {
            axios.get('/api/sites').then(response => {
                commit('setSites', response.data);
                resolve(response);
            }).catch(error => reject(error));
        }),
        edit: ({ commit }, site) => {
            commit('setEditorSite', site);
        },
        create: ({ commit, state }) => new Promise((resolve, reject) => {
            axios.post('/api/sites', state.site).then(response => {
                commit('addSite', response.data);
                commit('clearMessages');
                commit('setSuccess', `The site '${response.data.name}' has been created.`);
                resolve(response);
            }).catch(error => reject(error));
        }),
        update: ({ commit }, site) => {
            axios.put(`/api/sites/${site.id}`, site.data).then(response => {
                commit('clearMessages');
                commit('updateSite', {
                    id: site.id,
                    site: response.data,
                });
                commit('setSuccess', `The site '${site.data.name}' has been saved.`);
            }).catch(error => {
                const res = error.response;

                commit('clearMessages');

                if (res && HTTP_UNPROCESSABLE_ENTITY === res.status) {
                    commit('setErrors', {
                        message: 'Fix the validation errors below and try again.',
                        errors: res.data.errors,
                    });
                } else if (res) {
                    commit('setErrors', res.statusText);
                } else {
                    commit('setErrors', error.message);
                }
            });
        },
    },
    getters: {
        all: state => state.sites,
        filtered: state => state.sites.filter(
            site => site.name.toLowerCase().includes(state.currentFilter.toLowerCase()),
        ),
        findById: state => id => state.sites.find(s => s.id === id),
        findByDocroot: state => path => state.sites.find(s => s.project_root === path),
    },
};
