export default {
    namespaced: true,
    state: {
        button: null,
        isVisible: false,
        output: '',
        percentComplete: 5,
        steps: [],
        title: 'Loading...',
    },
    mutations: {
        addStep: (state, { name, text }) => {
            state.steps.push({ name, text, icon: 'minus disabled' });
        },
        appendOutput: (state, text) => {
            state.output += text;
        },
        completeStep: (state, step) => {
            const index = state.steps.findIndex(s => s.name === step);

            Vue.set(state.steps, index, { ...state.steps[index], icon: 'check' });
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
        monitor: ({ commit }, { channel, item }) => {
            window.Echo
                .channel(`${channel}.${item}`)
                .listen('.progress', e => {
                    commit('appendOutput', e.text);
                });
        },
        progress: ({ commit }, { step, progress }) => {
            commit('completeStep', step);
            commit('setProgress', progress);
        },
    },
    getters: {
        button: state => state.button,
        done: state => state.percentComplete,
        output: state => state.output,
        title: state => state.title,
        steps: state => state.steps,
        visible: state => state.isVisible,
    },
};
