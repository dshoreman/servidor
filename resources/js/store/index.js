import Auth from './modules/Auth';
import Database from './modules/Database';
import FileEditor from './modules/FileEditor';
import FileManager from './modules/FileManager';
import Group from './modules/System/Group';
import Progress from './modules/Progress';
import Project from './modules/Project';
import User from './modules/System/User';
import Vue from 'vue';
import VueX from 'vuex';

Vue.use(VueX);

export default new VueX.Store({
    modules: {
        Auth,
        progress: Progress,
        projects: Project,
        databases: Database,
        files: FileManager,
        editor: FileEditor,
        systemGroups: Group,
        systemUsers: User,
    },
});
