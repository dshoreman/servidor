export default {
    namespaced: true,
    state: {
        button: null,
        isVisible: false,
        percentComplete: 5,
        steps: [],
        title: 'Loading...',
    },
    mutations: {
        addStep: (state, { name, text }) => {
            state.steps.push({ name, text, icon: 'minus disabled' });
        },
        completeStep: (state, step) => {
            const index = state.steps.findIndex(s => s.name === step);

            Vue.set(state.steps, index, { ...state.steps[index], icon: 'check' });
        },
        skipStep: (state, step) => {
            const index = state.steps.findIndex(s => s.name === step);

            Vue.set(state.steps, index, { ...state.steps[index], icon: 'times' });
        },
        setButton: (state, button) => {
            state.button = button;
        },
        setProgress: (state, progress) => {
            state.percentComplete = progress;
        },
        setTitle: (state, title) => {
            state.title = title;
        },
        setVisible: (state, visibility) => {
            state.isVisible = visibility;
        },
    },
    actions: {
        activateButton: ({ commit }, button) => {
            commit('setButton', button);
        },
        load: ({ commit }, { title, steps }) => {
            commit('setTitle', title);

            steps.forEach(step => commit('addStep', step));

            commit('setVisible', true);
        },
        monitor: ({ commit }, { channel, item }) => new Promise((resolve, reject) => {
            window.Echo
                .private(`${channel}.${item}`)
                .subscribed(() => {
                    resolve();
                })
                .listen('.progress', e => {
                    const { name, status, progress } = e.step;

                    if ('pending' === status) {
                        commit('addStep', e.step);
                    } else {
                        commit('setProgress', progress);
                        commit('complete' === status ? 'completeStep' : 'skipStep', name);
                    }
                }).error(error => reject(error));
        }),
        progress: ({ commit }, { step, progress }) => {
            commit('completeStep', step);
            commit('setProgress', progress);
        },
    },
    getters: {
        button: state => state.button,
        done: state => state.percentComplete,
        title: state => state.title,
        steps: state => state.steps,
        visible: state => state.isVisible,
    },
};
