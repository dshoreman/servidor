import CodeMirror from 'codemirror'
import 'codemirror/mode/meta.js'

export default {
    namespaced: true,
    state: {
        modes: CodeMirror.modeInfo,
        selectedMode: '',
        options: {
            styleActiveLine: true,
            tabSize: 4,
            mode: '',
            theme: 'dracula',
            lineNumbers: true,
            line: true,
            lineWrapping: true,
        },
        themes: [
            { text: '3024 Day', value: '3024-day' },
            { text: '3024 Night', value: '3024-night' },
            { text: 'abcdef', value: 'abcdef' },
            { text: 'Ambiance Mobile', value: 'ambiance-mobile' },
            { text: 'Ambiance', value: 'ambiance' },
            { text: 'Base16 Dark', value: 'base16-dark' },
            { text: 'Base16 Light', value: 'base16-light' },
            { text: 'Bespin', value: 'bespin' },
            { text: 'Blackboard', value: 'blackboard' },
            { text: 'Cobalt', value: 'cobalt' },
            { text: 'Colorforth', value: 'colorforth' },
            { text: 'Darcula', value: 'darcula' },
            { text: 'Dracula', value: 'dracula' },
            { text: 'Duotone Dark', value: 'duotone-dark' },
            { text: 'Duotone Light', value: 'duotone-light' },
            { text: 'Eclipse', value: 'eclipse' },
            { text: 'Elegant', value: 'elegant' },
            { text: 'Erlang Dark', value: 'erlang-dark' },
            { text: 'Gruvbox Dark', value: 'gruvbox-dark' },
            { text: 'Hopscotch', value: 'hopscotch' },
            { text: 'Icecoder', value: 'icecoder' },
            { text: 'Idea', value: 'idea' },
            { text: 'Isotope', value: 'isotope' },
            { text: 'Lesser Dark', value: 'lesser-dark' },
            { text: 'Liquibyte', value: 'liquibyte' },
            { text: 'Lucario', value: 'lucario' },
            { text: 'Material Darker', value: 'material-darker' },
            { text: 'Material Ocean', value: 'material-ocean' },
            { text: 'Material Palenight', value: 'material-palenight' },
            { text: 'Material', value: 'material' },
            { text: 'Mbo', value: 'mbo' },
            { text: 'MDN like', value: 'mdn-like' },
            { text: 'Midnight', value: 'midnight' },
            { text: 'Monokai', value: 'monokai' },
            { text: 'Moxer', value: 'moxer' },
            { text: 'Neat', value: 'neat' },
            { text: 'Neo', value: 'neo' },
            { text: 'Night', value: 'night' },
            { text: 'Nord', value: 'nord' },
            { text: 'Oceanic Next', value: 'oceanic-next' },
            { text: 'Panda Syntax', value: 'panda-syntax' },
            { text: 'Paraiso Dark', value: 'paraiso-dark' },
            { text: 'Paraiso Light', value: 'paraiso-light' },
            { text: 'Pastel on Dark', value: 'pastel-on-dark' },
            { text: 'Railscasts', value: 'railscasts' },
            { text: 'Rubyblue', value: 'rubyblue' },
            { text: 'Seti', value: 'seti' },
            { text: 'Shadowfox', value: 'shadowfox' },
            { text: 'Solarized', value: 'solarized' },
            { text: 'SSMS', value: 'ssms' },
            { text: 'The Matrix', value: 'the-matrix' },
            { text: 'Tomorrow Night Bright', value: 'tomorrow-night-bright' },
            { text: 'Tomorrow Night Eighties', value: 'tomorrow-night-eighties' },
            { text: 'Twilight', value: 'twilight' },
            { text: 'Vibrant Ink', value: 'vibrant-ink' },
            { text: 'Xq Dark', value: 'xq-dark' },
            { text: 'Xq Light', value: 'xq-light' },
            { text: 'Yeti', value: 'yeti' },
            { text: 'Yonce', value: 'yonce' },
            { text: 'Zenburn', value: 'zenburn' },
        ],
    },
    mutations: {
        setTheme (state, theme) {
            state.options.theme = theme
        },
        setMode (state, mode) {
            state.options.mode = mode
        },
        setSelectedMode(state, name) {
            state.selectedMode = name
        },
        setLineWrapping(state, value) {
            state.options.lineWrapping = value
        }
    },
    actions: {
        async setTheme ({ commit }, theme) {
            await require('codemirror/theme/' + theme + '.css');
            commit('setTheme', theme)
        },
        async setMode ({ commit, state }, value) {
            var val = value, m, mode, spec;

            if (m = /.+\.([^.]+)$/.exec(val)) {
                var info = CodeMirror.findModeByExtension(m[1]);
                if (info) {
                    mode = info.mode;
                    spec = info.mime;
                    commit('setSelectedMode', info.mime)
                }
            } else {
                var info = CodeMirror.findModeByMIME(val);
                if (info) {
                    mode = info.mode;
                    spec = val;
                    commit('setSelectedMode', info.mime)
                }
            }

            if (mode) {
                if (mode !== "null") {
                    await require('codemirror/mode/' + mode + '/' + mode + '.js');
                }
                commit('setMode', mode)
            } else {
                commit('setSelectedMode', 'text/plain')
                commit('setMode', null)
            }
        },
        setLineWrapping ({ commit, state }, value) {
            commit('setLineWrapping', value);
        }
    },
    getters: {
        options: state => state.options,
        themes: state => state.themes,
        modes: state => state.modes,
        selectedMode: state => state.selectedMode,
    },
}
