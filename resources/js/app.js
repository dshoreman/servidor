import './bootstrap';
import Vue from 'vue';
import VueRouter from 'vue-router';
import SuiVue from 'semantic-ui-vue';
import routes from './routes'
import store from './store'

window.Vue = Vue;

Vue.use(VueRouter);
Vue.use(SuiVue);

Vue.component('main-menu', require('./components/MainMenu.vue'));
Vue.component('stats-bar', require('./components/StatsBar.vue'));

Vue.component('system-menu', require('./components/System/Menu.vue'));
Vue.component('system-groups', require('./components/System/Groups.vue'));
Vue.component('system-users', require('./components/System/Users.vue'));

Vue.component('passport-clients', require('./components/passport/Clients.vue'));
Vue.component('passport-authorized-clients', require('./components/passport/AuthorizedClients.vue'));
Vue.component('passport-personal-access-tokens', require('./components/passport/PersonalAccessTokens.vue'));

const router = new VueRouter({
    mode: 'history',
    routes
});

router.beforeEach((to, from, next) => {
    let authed = store.getters.loggedIn;

    if (!authed && to.matched.some(route => route.meta.auth)) {
        next({ name: 'login' });
    } else if (authed && to.matched.some(route => route.meta.guest)) {
        next({ name: 'dashboard' });
    } else {
        next();
    }
});

window.axios.interceptors.response.use(response => {
    return response;
}, error => {
    if (error.response.status === 401) {
        router.push({ name: 'login' });
    }

    return Promise.reject(error);
});

const app = new Vue({
    el: '#app',
    router,
    store
});
