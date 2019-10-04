import Vue from 'vue';
import VueX from 'vuex';
import Auth from './modules/Auth';
import Site from './modules/Site';
import FileManager from './modules/FileManager';
import Editor from './modules/Editor';
import Group from './modules/System/Group';
import User from './modules/System/User';

Vue.use(VueX);

export default new VueX.Store ({
    modules: {
        Auth,
        sites: Site,
        files: FileManager,
        editor: Editor,
        systemGroups: Group,
        systemUsers: User,
    }
});
