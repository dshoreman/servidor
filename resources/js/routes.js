import Dashboard from './pages/Dashboard.vue'
import Sites from './pages/Sites.vue'
import AppLayout from './layouts/App.vue'
import SystemLayout from './layouts/System.vue'
import SystemGroups from './components/System/Groups.vue'
import SystemUsers from './components/System/Users.vue'
import Login from './pages/Auth/Login.vue'
import Register from './pages/Auth/Register.vue'
import NotFound from './pages/NotFound.vue'

const routes = [{
    path: '/', component: AppLayout,
    children: [{
        component: Dashboard,
        name: 'dashboard',
        path: '/',
        meta: { auth: true },
    }, {
        component: Sites,
        name: 'apps',
        path: '/apps',
        meta: { auth: true },
    }, {
        path: '/system', component: SystemLayout,
        children: [{
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
