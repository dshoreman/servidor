export default {
    state: {
        alert: {
            title: '',
            msg: '',
        },
        token: localStorage.getItem('accessToken') || null,
        user: {},
    },
    mutations: {
        setAlert: (state, { msg, title }) => {
            state.alert.title = title;
            state.alert.msg = msg;
        },
        clearAlert: state => {
            state.alert = {};
        },
        setToken: (state, token) => {
            window.axios.defaults.headers.common.Authorization = `Bearer ${token}`;
            localStorage.setItem('accessToken', token);
            state.token = token;
        },
        clearToken: state => {
            localStorage.removeItem('accessToken');
            state.token = null;
        },
        setUser: (state, user) => {
            state.user = user;
        },
    },
    actions: {
        register: data => new Promise((resolve, reject) => {
            axios.post('/api/register', {
                name: data.name,
                email: data.email,
                password: data.password,
                password_confirmation: data.passwordConfirmation,
            }).then(response => {
                resolve(response);
            }).catch(error => {
                reject(error);
            });
        }),
        async login({ commit, dispatch }, credentials) {
            try {
                await axios.get('/csrf');
                await axios.post('/login', {
                    email: credentials.username,
                    password: credentials.password,
                });
                commit('clearAlert');

                return Promise.resolve();
            } catch (error) {
                commit('clearAlert');
                commit('setAlert', {
                    title: "We couldn't get you logged in :(",
                    msg: error.response.data.message,
                });

                return Promise.reject(error);
            }
        },
        logout: ({ commit }) => new Promise((resolve, reject) => {
            axios.post('/api/logout').then(response => {
                resolve(response);
            }).catch(error => {
                reject(error);
            }).then(() => {
                commit('clearToken');
            });
        }),
        fetchProfile: ({ commit }) => {
            axios.get('/api/user').then(response => {
                commit('setUser', response.data);
            });
        },
        forceLogin: ({ commit }, reason) => {
            commit('setAlert', { title: reason, msg: 'Please login again.' });
            commit('clearToken');
        },
    },
    getters: {
        authMsg: state => state.alert,
        token: state => state.token,
        loggedIn: state => null !== state.token,
    },
};
