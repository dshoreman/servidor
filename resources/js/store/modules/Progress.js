export default {
    namespaced: true,
    state: {
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
        load: ({ commit }, { title, steps }) => {
            commit('setTitle', title);

            steps.forEach(step => commit('addStep', step));

            commit('setVisible', true);
        },
        progress: ({ commit }, { step, progress }) => {
            commit('completeStep', step);
            commit('setProgress', progress);
        },
    },
    getters: {
        done: state => state.percentComplete,
        title: state => state.title,
        steps: state => state.steps,
        visible: state => state.isVisible,
    },
};
