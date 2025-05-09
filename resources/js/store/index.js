import { createStore } from 'vuex';
import axios from 'axios'; // Importar axios aquí también para usarlo en actions

export default createStore({
  state: {
    // Intentar leer el token y el usuario de localStorage al inicio
    token: localStorage.getItem('auth_token') || null, // Usar 'auth_token' consistente
    user: JSON.parse(localStorage.getItem('user')) || null // Leer el objeto user también
  },

  mutations: {
    SET_USER(state, user) {
      state.user = user;
      // Guardar el objeto user completo en localStorage
      localStorage.setItem('user', JSON.stringify(user));
    },
    SET_TOKEN(state, token) {
      state.token = token;
      // Guardar el token en localStorage
      localStorage.setItem('auth_token', token); // Usar 'auth_token' consistente
      // Configurar el header de autorización por defecto en Axios inmediatamente
      if (token) {
          axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      } else {
          // Limpiar el header si no hay token
          delete axios.defaults.headers.common['Authorization'];
      }
    },
    CLEAR_AUTH(state) {
      state.user = null;
      state.token = null;
      // Eliminar ambos items de localStorage
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      // Limpiar el header de autorización en Axios
      delete axios.defaults.headers.common['Authorization'];
    }
  },

  actions: {
    // Acción para intentar login a través de la API
    async login({ commit }, credentials) {
      try {
        // La llamada Axios ya usa la baseURL configurada en bootstrap.js (/api)
        const response = await axios.post('/login', credentials);

        // Commitear las mutaciones para actualizar el estado y localStorage
        commit('SET_TOKEN', response.data.token);
        commit('SET_USER', response.data.user);

        // Opcional: redirigir después del login exitoso (si no lo haces en el componente Login.vue)
        // router.push('/products');

        return response; // Devolver la respuesta para que el componente Login.vue la use
      } catch (error) {
        console.error('Login API error:', error.response || error);
        commit('CLEAR_AUTH'); // Limpiar cualquier estado de autenticación en caso de error de login
        throw error; // Relanzar el error para que el componente que llamó a esta acción lo maneje (ej: mostrar mensaje)
      }
    },

    // Acción para realizar logout (si el API tiene endpoint de logout)
    async logout({ commit, state }) {
        try {
            // Si hay un token, intentar llamar al endpoint de logout del API
            if (state.token) {
                 await axios.post('/logout'); // Asumiendo que tienes un endpoint POST /api/logout
            }
        } catch (error) {
             console.error('Logout API error:', error.response || error);
             // No lanzar error, queremos limpiar el estado local de todas formas
        } finally {
            // Limpiar el estado local y localStorage
            commit('CLEAR_AUTH');
            // Opcional: redirigir a la página de login después del logout
            // router.push('/login');
        }
    },

    // Acción para reestablecer el estado de autenticación al cargar la app
    async initializeAuth({ commit, state }) {
        // Si ya hay un token en el estado (leído de localStorage al inicio)
        if (state.token) {
            // Configurar el header de Authorization en Axios
            axios.defaults.headers.common['Authorization'] = `Bearer ${state.token}`;

            // Opcional: Intentar obtener la información del usuario desde la API
            // Esto verifica que el token sigue siendo válido y refresca la info del usuario
            if (!state.user) { // Solo si el objeto user no se pudo leer de localStorage (ej: localStorage solo guardaba el token)
                 try {
                    const response = await axios.get('/user'); // Asumiendo un endpoint GET /api/user que devuelve el usuario logueado
                    commit('SET_USER', response.data); // Asumiendo que /api/user devuelve el objeto user directamente
                 } catch (error) {
                    console.error('Error fetching user on init:', error.response || error);
                    // Si falla al obtener el usuario (token inválido/expirado), limpiar la sesión local
                    commit('CLEAR_AUTH');
                 }
            }
        } else {
            // Si no hay token en localStorage, limpiar cualquier rastro por si acaso
            commit('CLEAR_AUTH');
        }
    }
  },

  getters: {
    isAuthenticated: state => !!state.token, // Devuelve true si hay un token
    isAdmin: state => state.user?.role === 'admin', // Devuelve true si el usuario existe y su rol es 'admin'
    getUser: state => state.user // Devuelve el objeto usuario
  }
});