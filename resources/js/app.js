import './bootstrap';
import Vue from 'vue';
import SuiVue from 'semantic-ui-vue';

window.Vue = Vue;

Vue.use(SuiVue);

Vue.component('main-menu', require('./components/MainMenu.vue'));
Vue.component('stats-bar', require('./components/StatsBar.vue'));

Vue.component('system-menu', require('./components/System/Menu.vue'));
Vue.component('system-groups', require('./components/System/Groups.vue'));
Vue.component('system-users', require('./components/System/Users.vue'));

const app = new Vue({
    el: '#app'
});
