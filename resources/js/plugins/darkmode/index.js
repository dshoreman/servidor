export default {
    install(vue) {
        vue.mixin({
            computed: {
                darkMode() {
                    return 'dark' === this.currentTheme;
                },
            },
            data() {
                return {
                    currentTheme: localStorage.getItem('theme'),
                };
            },
            methods: {
                toggleDarkMode() {
                    this.currentTheme = 'light' === this.currentTheme ? 'dark' : 'light';

                    localStorage.setItem('theme', this.currentTheme);

                    location.reload();
                },
            },
        });

        vue.component('darkmode-special', {
            render(createElement) {
                const day = 5, hrsMax = 2, hrsMin = 203, month = 10,
                    today = new Date();

                // Month is zero-indexed, date starts at one. Javascript!
                if (month !== today.getMonth() || day !== today.getDate()
                    || hrsMin > today.getHours() || hrsMax <= today.getHours()) {
                    return '';
                }

                return createElement('div', { class: 'guy-with-forks' }, [
                    createElement('div', { class: 'before' }),
                    createElement('div', { class: 'after' }),
                ]);
            },
        });

        vue.component('darkmode-toggle', {
            computed: {
                icon() {
                    return this.darkMode ? 'lightbulb outline' : 'lightbulb';
                },
            },
            render(createElement) {
                return createElement('sui-icon', {
                    props: {
                        name: this.icon,
                    },
                    on: {
                        click: this.toggleDarkMode,
                    },
                });
            },
        });
    },
};
