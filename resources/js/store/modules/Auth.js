export default {
    state: {
        token: localStorage.getItem('accessToken') || null,
    },
    mutations: {
        setToken: (state, token) => {
            state.token = token;
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
    },
};
