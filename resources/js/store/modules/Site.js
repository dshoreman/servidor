export default {
    state: {
        alerts: [],
        current: {},
        currentFilter: '',
        errors: [],
        sites: [],
        site: {
            name: '',
        },
    },
    mutations: {
        setSuccess: (state, message) => {
            state.alerts.push({
                title: 'Success!',
                message: message,
                isSuccess: true,
            });
        },
        setErrors: (state, {message, errors, action = 'save'}) => {
            state.alerts.push({
                title: 'Could not ' + action + ' Site!',
                message: message,
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
            const site = {...state.sites.find(s => s.id === id)};

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
        updateSite: (state, {id, site}) => {
            let index = state.sites.findIndex(s => s.id === id);

            Vue.set(state.sites, index, site);
        },
        removeSite: (state, id) => {
            let index = state.sites.findIndex(s => s.id === id);

            state.sites.splice(index, 1);
        },
    },
    actions: {
        loadSites: ({commit}) => {
            return new Promise((resolve, reject) =>
                axios.get('/api/sites') .then(response => {
                    commit('setSites', response.data);
                    resolve(response);
                }).catch(error => reject(error))
            );
        },
        editSite: ({commit}, site) => {
            commit('setEditorSite', site);
        },
        createSite: ({commit, state}) => {
            axios.post('/api/sites', state.site).then(response => {
                commit('addSite', response.data);
                commit('clearMessages');
                commit('setSuccess', "The site '" + site.name + "' has been created.");
            });
        },
        updateSite: ({commit}, site) => {
            axios.put('/api/sites/'+site.id, site.data).then(response => {
                commit('clearMessages');
                commit('updateSite', {
                    id: site.id,
                    site: response.data
                });
                commit('setSuccess', "The site '" + site.data.name + "' has been saved.");
            }).catch(error => {
                const res = error.response;
                commit('clearMessages');

                if (res && res.status === 422) {
                    commit('setErrors', {
                        message: "Fix the validation errors below and try again.",
                        errors: res.data.errors
                    });
                } else if (res) {
                    commit('setErrors', res.statusText);
                } else {
                    commit('setErrors', error.message);
                }
            });
        },
        deleteSite: ({commit, state}, id) => {
            return new Promise((resolve, reject) => {
                axios.delete('/api/sites/' + id).then(response => {
                    commit('removeSite', id);
                    resolve(response);
                }).catch(error => {
                    commit('setErrors', {
                        message: error.message,
                        action: 'delete',
                    });
                    reject(error);
                });
            });
        },
    },
    getters: {
        filteredSites: state => {
            return state.sites.filter(site => {
                return site.name.toLowerCase().includes(state.currentFilter.toLowerCase());
            });
        },
        getSiteByDocroot: (state) => (path) => {
            return state.sites.find(s => s.document_root == path);
        },
        sites: state => {
            return state.sites;
        },
    },
}
