import Vue from 'vue';
import VueRouter from 'vue-router';

import LoginComponent from './components/LoginComponent';
import AdminComponent from './components/AdminComponent';
import RolesComponent from './components/RolesComponent';


 Vue.use(VueRouter);

const routes = [
    {
        path:'/',
        redirect: '/login',

    },

    {
        path:'/login',
        component: LoginComponent,
        name: 'Login'
    },
    {
        path: '/admin',
        component: AdminComponent,
        name: 'Admin',


        //path: admin/roles
        children: [
            {
                path: 'roles',
                component: RolesComponent,
                name: 'Roles'
            },
        ],

        //not needed here because globally guarded
        beforeEnter: (to, from, next) => {
          axios.get('api/verify')
            .then( res => next() )
            .catch( error => next('/login') )
          }
    },
]

const router = new VueRouter({routes})
//check token for each routes before entering into
//globally guarded
router.beforeEach((to, from, next) => {
   const token = localStorage.getItem('token') || null
    console.log("Token: ", token)
    window.axios.defaults.headers['Authorization'] = "Bearer " + token;
    next();
});

export default router
