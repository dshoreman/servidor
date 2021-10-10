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
        addStep: (state, { name, text, status }) => {
            state.steps.push({ name,
                text,
                icon: 'working' === status ? 'loading spinner' : 'minus disabled',
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
            commit('setButton', { route, text: 'Continue' });
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
                    const { name, status, progress } = e.step,
                        complete = 'complete' === status,
                        progressAction = complete ? 'stepCompleted' : 'stepSkipped';

                    'working' === status
                        ? commit('addStep', e.step)
                        : dispatch(progressAction, { progress, step: name });
                }).error(error => reject(error));
        }),
        progress: ({ commit }, { progress }) => {
            commit('setProgress', progress);
        },
        start: ({ commit }, { step }) => {
            commit('setIcon', { step, icon: 'loading spinner' });
        },
        stepCompleted({ commit }, { step, progress = 0 }) {
            if (0 < progress) {
                commit('setProgress', progress);
            }
            commit('setIcon', { step, icon: 'check', colour: 'green' });
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
