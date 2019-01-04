import Dashboard from './pages/Dashboard.vue'
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
    }, {
        path: '/system', component: SystemLayout,
        children: [{
            component: SystemGroups,
            name: 'system.groups',
            path: '/system/groups',
        }, {
            component: SystemUsers,
            name: 'system.users',
            path: '/system/users',
        }],
    }],
}, {
    component: Login,
    name: 'login',
    path: '/login',
}, {
    component: Register,
    name: 'register',
    path: '/register',
}, {
    path: '*', component: NotFound,
}];

export default routes;
