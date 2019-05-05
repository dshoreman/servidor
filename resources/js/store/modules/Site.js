export default {
    state: {
        sites: [],
        site: {
            name: '',
        },
    },
    mutations: {
        setSites: (state, sites) => {
            state.sites = sites;
        },
        addSite: (state, site) => {
            state.sites.push(site);
            state.site.name = '';
        },
        updateSite: (state, {id, site}) => {
            let index = state.sites.findIndex(s => s.id === id);

            Vue.set(state.sites, index, site);
        },
    },
    actions: {
        loadSites: ({commit}) => {
            axios.get('/api/sites').then(response => {
                commit('setSites', response.data);
            });
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
            });
        },
    },
    getters: {
        sites: state => {
            return state.sites;
        },
    },
}
