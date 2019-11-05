export default {
    install (vue, options = {}) {
        vue.mixin({
            computed: {
                darkMode: function() {
                    return this.currentTheme == 'dark';
                },
            },
            data() {
                return {
                    currentTheme: localStorage.getItem('theme'),
                };
            },
            methods: {
                toggleDarkMode: function() {
                    this.currentTheme = this.currentTheme == 'light' ? 'dark' : 'light';

                    localStorage.setItem('theme', this.currentTheme);

                    location.reload();
                },
            },
        });

        vue.component('darkmode-special', {
            render: function (createElement) {
                const today = new Date();

                // Month is zero-indexed, date starts at one. Javascript!
                if (today.getMonth() != 10 || today.getDate() != 5 ||
                    today.getHours() < 20 || today.getHours() >= 23) {
                    return;
                }

                return createElement('div', { class: 'guy-with-forks' }, [
                    createElement('div', { class: 'before' }),
                    createElement('div', { class: 'after' }),
                ]);
            },
        });

        vue.component('darkmode-toggle', {
            computed: {
                icon: function() {
                    return this.darkMode ? 'lightbulb outline' : 'lightbulb';
                },
            },
            render: function (createElement) {
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
    }
}
