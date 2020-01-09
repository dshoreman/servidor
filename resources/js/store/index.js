import Auth from './modules/Auth';
import Database from './modules/Database';
import FileEditor from './modules/FileEditor';
import FileManager from './modules/FileManager';
import Group from './modules/System/Group';
import Site from './modules/Site';
import User from './modules/System/User';
import Vue from 'vue';
import VueX from 'vuex';

Vue.use(VueX);

export default new VueX.Store ({
    modules: {
        Auth,
        sites: Site,
        databases: Database,
        files: FileManager,
        editor: FileEditor,
        systemGroups: Group,
        systemUsers: User,
    }
});
