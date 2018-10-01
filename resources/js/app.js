import './bootstrap';
import Vue from 'vue';
import SuiVue from 'semantic-ui-vue';

window.Vue = Vue;

Vue.use(SuiVue);

Vue.component('example-component', require('./components/ExampleComponent.vue'));

const app = new Vue({
    el: '#app'
});
