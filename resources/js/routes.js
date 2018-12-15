import Dashboard from './pages/Dashboard.vue'
import SystemLayout from './layouts/System.vue'
import SystemGroups from './components/System/Groups.vue'
import SystemUsers from './components/System/Users.vue'

const routes = [{
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
}];

export default routes;
