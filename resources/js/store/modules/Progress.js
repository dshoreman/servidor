const PERCENT_COMPLETE = 100;

export default {
    namespaced: true,
    state: {
        button: null,
        finalStep: null,
        isVisible: false,
        percentComplete: 0,
        steps: [],
        title: 'Loading...',
    },
    mutations: {
        addStep: (state, { name, text }) => {
            state.steps.push({ name, text, icon: 'minus disabled', colour: 'grey' });
        },
        completeStep: (state, step) => {
            const index = state.steps.findIndex(s => s.name === step);

            Vue.set(state.steps, index, { ...state.steps[index], icon: 'check', colour: 'green' });
        },
        skipStep: (state, step) => {
            const index = state.steps.findIndex(s => s.name === step);

            Vue.set(state.steps, index, { ...state.steps[index], icon: 'times', colour: 'grey' });
        },
        setButton: (state, button) => {
            state.button = button;
        },
        setFinalStep: (state, step) => {
            state.finalStep = step;
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
        load: ({ commit, state }, { title, steps, completeWhenDone = null }) => {
            commit('setTitle', title);

            steps.forEach(step => commit('addStep', step));

            if (state.steps.some(s => s.name === completeWhenDone)) {
                commit('setFinalStep', completeWhenDone);
            }

            commit('setVisible', true);
        },
        monitor: ({ commit, state }, { channel, item }) => new Promise((resolve, reject) => {
            window.Echo
                .private(`${channel}.${item}`)
                .subscribed(() => {
                    resolve();
                })
                .listen('.progress', e => {
                    const { name, status, progress } = e.step,
                        complete = 'complete' === status;

                    if ('pending' === status) {
                        commit('addStep', e.step);
                    } else {
                        commit(complete ? 'completeStep' : 'skipStep', name);

                        if (complete && PERCENT_COMPLETE === progress && state.finalStep) {
                            commit('completeStep', state.finalStep);
                        }

                        commit('setProgress', progress);
                    }
                }).error(error => reject(error));
        }),
        progress: ({ commit }, { step = '', progress }) => {
            if ('' !== step) {
                commit('completeStep', step);
            }

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
