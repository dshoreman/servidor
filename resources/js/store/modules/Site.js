export default {
    namespaced: true,
    state: {
        alerts: [],
        branches: [],
        branchesLoading: false,
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
        setErrors: (state, { message, errors, action = 'save' }) => {
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
            const site = { ...state.sites.find(s => s.id === id) };

            // Shitty hack because SemanticUI-Vue doesn't support a simple
            // goddamn Number value for its sui-checkbox component. Wtf?!
            if (site.redirect_type) {
                site.redirect_type = site.redirect_type.toString();
            }

            state.current = site;
        },
        setSiteBranches: (state, branches) => {
            state.branches = branches;
            state.branchesLoading = false;
        },
        branchesLoading: (state) => {
            state.branchesLoading = true;
        },
        addSite: (state, site) => {
            state.sites.push(site);
            state.site.name = '';
        },
        updateSite: (state, { id, site }) => {
            const index = state.sites.findIndex(s => s.id === id);

            Vue.set(state.sites, index, site);
        },
        removeSite: (state, id) => {
            const index = state.sites.findIndex(s => s.id === id);

            state.sites.splice(index, 1);
        },
    },
    actions: {
        load: ({ commit }) => {
            return new Promise((resolve, reject) => {
                axios.get('/api/sites') .then(response => {
                    commit('setSites', response.data);
                    resolve(response);
                }).catch(error => reject(error));
            });
        },
        loadBranches: ({ commit, state }, repo = '') => {
            commit('branchesLoading');
            let url = '/api/sites/' + state.current.id + '/branches';

            if ('' !== repo) {
                url += '?repo=' + repo;
            }

            return new Promise((resolve, reject) => {
                axios.get(url).then(response => {
                    commit('setSiteBranches', response.data);
                    resolve(response);
                }).catch(error => reject(error));
            });
        },
        edit: ({ commit, dispatch }, site) => {
            commit('setEditorSite', site);
            dispatch('loadBranches');
        },
        create: ({ commit, state }) => {
            return new Promise((resolve, reject) => {
                axios.post('/api/sites', state.site).then(response => {
                    commit('addSite', response.data);
                    commit('clearMessages');
                    commit('setSuccess', "The site '" + response.data.name + "' has been created.");
                    resolve(response);
                }).catch(error => reject(error));
            });
        },
        update: ({ commit }, site) => {
            axios.put('/api/sites/'+site.id, site.data).then(response => {
                commit('clearMessages');
                commit('updateSite', {
                    id: site.id,
                    site: response.data,
                });
                commit('setSuccess', "The site '" + site.data.name + "' has been saved.");
            }).catch(error => {
                const res = error.response;
                commit('clearMessages');

                if (res && 422 === res.status) {
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
        pull: (site) => {
            return axios.post('/api/sites/'+site.id+'/pull');
        },
        delete: ({ commit }, id) => {
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
        all: state => {
            return state.sites;
        },
        filtered: state => {
            return state.sites.filter(site => {
                return site.name.toLowerCase().includes(state.currentFilter.toLowerCase());
            });
        },
        findById: (state) => (id) => {
            return state.sites.find(s => s.id === id);
        },
        findByDocroot: (state) => (path) => {
            return state.sites.find(s => s.document_root === path);
        },
        branchOptions: (state) => {
            return state.branches.map(b => {
                return { text: b, value: b };
            });
        },
    },
};
