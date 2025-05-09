// resources/js/bootstrap.js
import axios from 'axios';
import _ from 'lodash';

// Configuración global de Axios
window._ = _;
window.axios = axios;

// Configuración BASE CRUCIAL para XAMPP
window.axios.defaults.baseURL = '/api';
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

// Interceptor para manejar errores globalmente
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);