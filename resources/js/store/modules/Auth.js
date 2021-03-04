export default {
    state: {
        alert: {
            title: '',
            msg: '',
        },
        user: sessionStorage.user
            ? JSON.parse(sessionStorage.getItem('user'))
            : {},
    },
    mutations: {
        setAlert: (state, { msg, title }) => {
            state.alert.title = title;
            state.alert.msg = msg;
        },
        clearAlert: state => {
            state.alert = {};
        },
        setUser: (state, user) => {
            sessionStorage.setItem('user', JSON.stringify(user));
            state.user = user;
        },
        clearUser: state => {
            sessionStorage.removeItem('user');
            state.user = {};
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
                await dispatch('fetchProfile');

                return Promise.resolve();
            } catch (error) {
                commit('setAlert', {
                    title: "We couldn't get you logged in :(",
                    msg: 'response' in error
                        ? error.response.data.message
                        : error.message,
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
                commit('clearUser');
            });
        }),
        async fetchProfile({ commit }) {
            const response = await axios.get('/api/user');

            commit('setUser', response.data);
        },
        forceLogin: ({ commit }, reason) => {
            commit('setAlert', { title: reason, msg: 'Please login again.' });
            commit('clearUser');
        },
    },
    getters: {
        authMsg: state => state.alert,
        loggedIn: state => 0 !== Object.keys(state.user).length,
    },
};
