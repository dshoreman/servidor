export default {
    state: {
        token: localStorage.getItem('accessToken') || null,
        user: {},
    },
    mutations: {
        setToken: (state, token) => {
            window.axios.defaults.headers.common['Authorization'] = 'Bearer '+token;
            localStorage.setItem('accessToken', token);
            state.token = token;
        },
        clearToken: (state) => {
            localStorage.removeItem('accessToken');
            state.token = null;
        },
        setUser: (state, user) => {
            state.user = user;
        },
    },
    actions: {
        register: ({commit}, data) => {
            return new Promise((resolve, reject) => {
                axios.post('/api/register', {
                    name: data.name,
                    email: data.email,
                    password: data.password,
                    password_confirmation: data.passwordConfirmation,
                }).then(response => {
                    resolve(response);
                }).catch(error => {
                    reject(error);
                });;
            });
        },
        login: ({commit}, data) => {
            return new Promise((resolve, reject) => {
                axios.post('/api/login', {
                    username: data.username,
                    password: data.password,
                }).then(response => {
                    commit('setToken', response.data.access_token);
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
                    commit('clearToken');
                });
            });
        },
        fetchProfile: ({commit}) => {
            axios.get('/api/user').then(response => {
                commit('setUser', response.data);
            });
        },
    },
    getters: {
        token: state => state.token,
        loggedIn: state => {
            return state.token !== null;
        },
    },
};
