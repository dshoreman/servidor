export default {
    state: {
        error: '',
        errors: [],
        sites: [],
        site: {
            name: '',
        },
        current: {},
    },
    mutations: {
        setErrors: (state, {message, errors}) => {
            state.error = message;

            if (errors) {
                state.errors = errors;
            }
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
            });
        },
        updateSite: ({commit}, site) => {
            axios.put('/api/sites/'+site.id, site.data).then(response => {
                commit('updateSite', {
                    id: site.id,
                    site: response.data
                });
            }).catch(error => {
                const res = error.response;

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
            axios.delete('/api/sites/' + id).then(response => {
                commit('removeSite', id)
            });
        },
    },
    getters: {
        sites: state => {
            return state.sites;
        },
    },
}
