import Dashboard from './pages/Dashboard.vue'
import Databases from './pages/Databases.vue'
import FileBrowser from './pages/Files/Browser.vue'
import FileEditor from './pages/Files/Editor.vue'
import Sites from './pages/Sites.vue'
import SiteList from './pages/Sites/List.vue'
import SiteEditor from './pages/Sites/Edit.vue'
import SiteViewer from './pages/Sites/Detail.vue'
import SystemGroups from './components/System/Groups.vue'
import SystemUsers from './components/System/Users.vue'
import Layout from './layouts/Servidor.vue'
import Login from './pages/Auth/Login.vue'
import Register from './pages/Auth/Register.vue'
import NotFound from './pages/NotFound.vue'

const routes = [{
    path: '/', component: Layout,
    children: [{
        component: Dashboard,
        name: 'dashboard',
        path: '/',
        meta: { auth: true },
    }, {
        path: '/apps', component: Sites,
        children: [{
            component: SiteList,
            name: 'apps',
            path: '/',
            meta: { auth: true },
        }, {
            component: SiteViewer,
            name: 'apps.view',
            path: '/apps/:id',
            meta: { auth: true },
            props: (route) => {
                let id = parseInt(route.params.id);

                if (Number.isNaN(id) || id < 0) {
                    return { id: 0 };
                }

                return { id: id };
            },
        }, {
            component: SiteEditor,
            name: 'apps.edit',
            path: '/apps/:id/edit',
            meta: { auth: true },
            props: (route) => {
                let id = parseInt(route.params.id);

                if (Number.isNaN(id) || id < 0) {
                    return { id: 0 };
                }

                return { id: id };
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
        props: (route) => ({ filePath: route.query.f }),
    }, {
        component: FileBrowser,
        name: 'files',
        path: '/files/:path?',
        meta: { auth: true },
        props: (route) => ({
            path: route.params.path ? route.params.path : '/var/www'
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
