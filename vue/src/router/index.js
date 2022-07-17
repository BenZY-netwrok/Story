
import {createRouter, createWebHistory} from "vue-router";
import Dashboard from '../views/Dashboard.vue';
import Login from "../views/Login.vue";
import Register from "../views/Register.vue";
import PostView from "../views/PostView.vue";
import DefaultLayout from '../components/DefaultLayout.vue';
import AuthLayout from '../components/AuthLayout.vue';
import Myforum from '../views/Myforum.vue';
import store from "../store";


const routes = [
    {
        path: '/',
        redirect: '/dashboard',
        name: 'Dashboard',
        component: DefaultLayout,
        meta: {requiresAuth: true},
        children: [
            {path: '/dashboard', name: 'Dashboard', component: Dashboard},
            {path: '/myforum', name: 'Myforum', component: Myforum},
            {path: '/posts/create/:create', name: 'PostCreate', component: PostView},
            {path: '/posts/:id', name: 'PostView', component: PostView},
        ]
    },
    {
        path: '/auth',
        redirect: '/login',
        name: 'Auth',
        component: AuthLayout,
        meta: {isGuest: true},
        children: [
            {
                path: '/login',
                name: 'Login',
                component: Login
            },
            {
                path: '/register',
                name: 'Register',
                component: Register
            },
        ]
    },

]

const router = createRouter({
    history: createWebHistory(),
    routes
})

//redirect user to the login or register page
router.beforeEach((to, from, next) => {
    if(to.meta.requiresAuth && !store.state.user.token) {
        next({name: 'Login'});
    }else if(store.state.user.token && to.meta.isGuest){
        next({name: 'Dashboard'});
    }else {
        next();
    }
})
export default router;