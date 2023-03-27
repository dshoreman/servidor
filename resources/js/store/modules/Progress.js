export default {
    namespaced: true,
    state: {
        button: null,
        isVisible: false,
        percentComplete: 0,
        steps: [],
        title: 'Loading...',
    },
    mutations: {
        addStep: (state, { name, text }) => {
            state.steps.push({ name,
                text,
                icon: 'minus disabled',
                colour: 'grey' });
        },
        setup: (state, title) => {
            state.title = title;
            state.button = null;
            state.steps = [];
        },
        setButton: (state, button) => {
            state.button = button;
        },
        setFinalStep: (state, step) => {
            state.finalStep = step;
        },
        setIcon: (state, { step, icon, colour }) => {
            const index = state.steps.findIndex(s => s.name === step);

            Vue.set(state.steps, index, { ...state.steps[index], icon, colour });
        },
        setProgress: (state, progress) => {
            state.percentComplete = progress;
        },
        setVisible: (state, visibility) => {
            state.isVisible = visibility;
        },
    },
    actions: {
        activateContinueButton: ({ commit }, route) => {
            commit('setButton', { route, colour: 'green', text: 'Continue' });
        },
        hide: ({ commit }) => {
            commit('setVisible', false);
        },
        load: ({ commit }, { title, steps }) => {
            commit('setup', title);

            steps.forEach(step => commit('addStep', step));

            commit('setVisible', true);
        },
        monitor: ({ commit, dispatch }, { channel, item }) => new Promise((resolve, reject) => {
            window.Echo
                .private(`${channel}.${item}`)
                .subscribed(() => {
                    resolve();
                })
                .listen('.progress', e => {
                    const data = { progress: e.step.progress, step: e.step.name };

                    switch (e.step.status) {
                    case 'skipped':
                        dispatch('stepSkipped', data);
                        break;
                    case 'working':
                        dispatch('stepStarted', data);
                        break;
                    case 'complete':
                        dispatch('stepCompleted', data);
                        break;
                    default:
                        commit('addStep', e.step);
                    }
                }).error(error => reject(error));
        }),
        progress: ({ commit }, { progress }) => {
            commit('setProgress', progress);
        },
        stepCompleted({ commit, state }, { step, progress = 0 }) {
            const maxdiff = 40;

            // Prevents an issue where config reload sets 100% progress too soon
            if (0 < progress && progress < maxdiff + state.percentComplete) {
                commit('setProgress', progress);
            }
            commit('setIcon', { step, icon: 'check', colour: 'green' });
        },
        stepStarted({ commit }, { step }) {
            commit('setIcon', { step, icon: 'loading spinner' });
        },
        stepSkipped({ commit }, { step, progress = 0 }) {
            if (0 < progress) {
                commit('setProgress', progress);
            }
            commit('setIcon', { step, icon: 'times', colour: 'grey' });
        },
        stepFailed: ({ commit }, { step, canBeFixed = false, fallback = {}}) => {
            commit('setIcon', { step, icon: 'times', colour: 'red' });
            commit(
                'setButton',
                canBeFixed
                    ? { action: 'progress/hide', colour: 'red', text: 'Fix errors' }
                    : { route: fallback.route, colour: '', text: fallback.text },
            );
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
