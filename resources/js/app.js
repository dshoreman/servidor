import './bootstrap';
import DarkMode from './plugins/darkmode';
import MainMenu from './components/MainMenu.vue';
import StatsBar from './components/StatsBar.vue';
import SuiVue from 'semantic-ui-vue';
import SystemGroups from './components/System/Groups.vue';
import SystemUsers from './components/System/Users.vue';
import Vue from 'vue';
import VueRouter from 'vue-router';
import routes from './routes';
import store from './store';

const HTTP_UNAUTHORISED = 401;

window.Vue = Vue;

Vue.use(VueRouter);
Vue.use(SuiVue);
Vue.use(DarkMode);

Vue.component('main-menu', MainMenu);
Vue.component('stats-bar', StatsBar);

Vue.component('system-groups', SystemGroups);
Vue.component('system-users', SystemUsers);

const router = new VueRouter({
    mode: 'history',
    routes,
});

router.beforeEach(async (to, from, next) => {
    const authed = store.getters.loggedIn, nextpage = {};

    if (!authed && to.matched.some(route => route.meta.auth)) {
        await store.dispatch('fetchProfile');

        if (!store.getters.loggedIn) {
            nextpage.name = 'login';
        }
    } else if (authed && to.matched.some(route => route.meta.guest)) {
        nextpage.name = 'dashboard';
    }

    next(nextpage);
});

window.axios.interceptors.response.use(response => response, error => {
    if (HTTP_UNAUTHORISED === error.response.status
        && 'invalid_credentials' !== error.response.data.error) {
        store.dispatch('forceLogin', 'Session timed out');
        router.push({ name: 'login' });
    }

    return Promise.reject(error);
});

const app = new Vue({
    el: '#app',
    router,
    store,
});
