import './bootstrap';
import DarkMode from './plugins/darkmode';
import MainMenu from './components/MainMenu.vue';
import PassportAccessTokens from './components/passport/PersonalAccessTokens.vue';
import PassportAuthorizedClients from './components/passport/AuthorizedClients.vue';
import PassportClients from './components/passport/Clients.vue';
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

Vue.component('passport-clients', PassportClients);
Vue.component('passport-authorized-clients', PassportAuthorizedClients);
Vue.component('passport-personal-access-tokens', PassportAccessTokens);

const router = new VueRouter({
    mode: 'history',
    routes,
});

router.beforeEach((to, from, next) => {
    const token = store.getters.token;
    let authed = store.getters.loggedIn, nextpage;

    if (token && token !== localStorage.getItem('accessToken')) {
        store.dispatch('forceLogin', 'Token mismatch');
        authed = false;
    }

    if (!authed && to.matched.some(route => route.meta.auth)) {
        nextpage = { name: 'login' };
    } else if (authed && to.matched.some(route => route.meta.guest)) {
        nextpage = { name: 'dashboard' };
    }

    next(nextpage);
});

window.axios.interceptors.response.use(response => {
    return response;
}, error => {
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
