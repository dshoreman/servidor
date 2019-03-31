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
    },
    getters: {
        sites: state => {
            return state.sites;
        },
    },
}
