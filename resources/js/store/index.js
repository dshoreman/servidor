import Vue from 'vue';
import VueX from 'vuex';
import Group from './modules/System/Group';
import User from './modules/System/User';

Vue.use(VueX);

export default new VueX.Store ({
    modules: {
        Group,
        User,
    }
});
