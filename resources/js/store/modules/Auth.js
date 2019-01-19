export default {
    state: {
        token: localStorage.getItem('accessToken') || null,
    },
    mutations: {
        setToken: (state, token) => {
            state.token = token;
        },
        clearToken: (state) => {
            state.token = null;
        },
    },
    actions: {
        login: ({commit}, data) => {
            return new Promise((resolve, reject) => {
                axios.post('/api/login', {
                    username: data.username,
                    password: data.password,
                }).then(response => {
                    const token = response.data.access_token;

                    window.axios.defaults.headers.common['Authorization'] = 'Bearer '+token;
                    localStorage.setItem('accessToken', token);

                    commit('setToken', token);

                    resolve(response);
                }).catch(error => {
                    reject(error);
                });
            });
        },
        logout: ({commit}) => {
            return new Promise((resolve, reject) => {
                axios.post('/api/logout').then(response => {
                    resolve(response);
                }).catch(error => {
                    reject(error);
                }).then(() => {
                    localStorage.removeItem('accessToken');
                    commit('clearToken');
                });
            });
        },
    },
    getters: {
        loggedIn: state => {
            return state.token !== null;
        },
    },
};
