import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';
import vuetify from './vuetify';

// Configuración Axios adicional (redundante para asegurar)
import axios from 'axios';

const app = createApp(App);

app.use(router);
app.use(store);
app.use(vuetify);

// Inyectar axios globalmente
app.config.globalProperties.$axios = axios;

store.dispatch('initializeAuth').then(() => {
     app.mount('#app');
});