import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Login.vue';
import Products from '../components/Products.vue';

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: Login,
        meta: { requiresAuth: false }
    },
    {
        path: '/products',
        name: 'Products',
        component: Products,
        meta: { requiresAuth: true }
    },
    {
        path: '/',
        redirect: '/login'
    }
];

const router = createRouter({
    history: createWebHistory('/'),
    routes
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = localStorage.getItem('auth_token');
    
    if (to.meta.requiresAuth && !isAuthenticated) {
        next('/login');
    } else {
        next();
    }
});

export default router;