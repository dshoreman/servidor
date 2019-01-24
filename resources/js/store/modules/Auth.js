export default {
    state: {
        token: localStorage.getItem('accessToken') || null,
        user: '',
    },
    mutations: {
        setToken: (state, token) => {
            state.token = token;
        },
        clearToken: (state) => {
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
        fetchProfile: ({commit}) => {
            axios.get('/api/user').then(response => {
                commit('setUser', response.data);
            });
        },
    },
    getters: {
        loggedIn: state => {
            return state.token !== null;
        },
    },
};
