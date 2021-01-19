export default {
    namespaced: true,
    state: {
        current: {},
        sites: [],
        site: {
            name: '',
            create_site: false,
        },
    },
    mutations: {
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
                resolve(response);
            }).catch(error => reject(error));
        }),
        update: ({ commit }, site) => {
            axios.put(`/api/sites/${site.id}`, site.data).then(response => {
                commit('updateSite', {
                    id: site.id,
                    site: response.data,
                });
            });
        },
    },
    getters: {
        all: state => state.sites,
        findById: state => id => state.sites.find(s => s.id === id),
        findByDocroot: state => path => state.sites.find(s => s.project_root === path),
    },
};
