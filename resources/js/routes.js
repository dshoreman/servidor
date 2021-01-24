import Databases from './pages/Databases.vue';
import FileBrowser from './pages/Files/Browser.vue';
import FileEditor from './pages/Files/Editor.vue';
import Layout from './layouts/Servidor.vue';
import Login from './pages/Auth/Login.vue';
import NotFound from './pages/NotFound.vue';
import ProjectCreator from './pages/Projects/NewProject.vue';
import ProjectIndex from './pages/Projects.vue';
import ProjectList from './pages/Projects/ProjectList.vue';
import ProjectViewer from './pages/Projects/ProjectViewer.vue';
import Register from './pages/Auth/Register.vue';
import SystemGroups from './components/System/Groups.vue';
import SystemUsers from './components/System/Users.vue';

const routes = [{
    path: '/',
    component: Layout,
    children: [{
        name: 'dashboard',
        path: '/',
        redirect: 'projects',
        meta: { auth: true },
    }, {
        path: '/projects',
        component: ProjectIndex,
        children: [{
            component: ProjectList,
            name: 'projects',
            path: '',
            meta: { auth: true },
        }, {
            component: ProjectCreator,
            name: 'projects.new',
            path: 'new',
            meta: { auth: true },
        }, {
            component: ProjectViewer,
            name: 'projects.view',
            path: ':id',
            meta: { auth: true },
            props: route => {
                const id = parseInt(route.params.id);

                if (Number.isNaN(id) || 0 > id) {
                    return { id: 0 };
                }

                return { id };
            },
        }],
    }, {
        component: Databases,
        name: 'databases',
        path: '/databases',
        meta: { auth: true },
    }, {
        component: FileEditor,
        name: 'files.edit',
        path: '/files/edit',
        meta: { auth: true },
        props: route => ({ filePath: route.query.f }),
    }, {
        component: FileBrowser,
        name: 'files',
        path: '/files/:path?',
        meta: { auth: true },
        props: route => ({
            path: route.params.path ? route.params.path : '/var/www',
        }),
    }, {
        component: SystemGroups,
        name: 'system.groups',
        path: '/system/groups',
        meta: { auth: true },
    }, {
        component: SystemUsers,
        name: 'system.users',
        path: '/system/users',
        meta: { auth: true },
    }],
}, {
    component: Login,
    name: 'login',
    path: '/login',
    meta: { guest: true },
}, {
    component: Register,
    name: 'register',
    path: '/register',
    meta: { guest: true },
}, {
    path: '*', component: NotFound,
}];

export default routes;
