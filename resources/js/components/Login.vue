<template>
    <v-container>
        <v-card max-width="500" class="mx-auto mt-5">
            <v-card-title class="text-center">Login</v-card-title>
            <v-card-text>
                <v-form @submit.prevent="login">
                    <v-text-field
                        v-model="form.email"
                        label="Email"
                        type="email"
                        required
                        outlined
                        dense
                    ></v-text-field>
                    <v-text-field
                        v-model="form.password"
                        label="Password"
                        type="password"
                        required
                        outlined
                        dense
                    ></v-text-field>
                    <v-btn
                        type="submit"
                        color="primary"
                        block
                        large
                        :loading="loading"
                        :disabled="loading"
                    >
                        Login
                    </v-btn>
                </v-form>
                <v-alert
                    v-if="error"
                    type="error"
                    dense
                    text
                    class="mt-3"
                >
                    {{ error }}
                </v-alert>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
// No necesitas importar axios aquí si la llamada se hace a través del store
// import axios from 'axios';

export default {
    data() {
        return {
            form: {
                email: '',
                password: ''
            },
            loading: false, // Controla el estado de carga del botón
            error: '' // Muestra mensajes de error al usuario
        };
    },
    methods: {
        async login() {
            this.loading = true; // Activar estado de carga al iniciar la petición
            this.error = ''; // Limpiar cualquier error anterior

            try {
                // Llama a la acción 'login' del Vuex store
                // La acción del store se encarga de la llamada API, guardar token/user,
                // y configurar el header de Axios.
                await this.$store.dispatch('login', this.form);

                // Si la acción del store fue exitosa, redirige
                // El store ya guardó el token y el usuario
                this.$router.push('/products');

            } catch (error) {
                console.error('Login error:', error.response || error);
                // Mostrar mensaje de error al usuario si la acción del store falló
                // La acción del store ya relanza el error recibido de la API
                this.error = error.response?.data?.message || 'Error al iniciar sesión. Verifica tus credenciales.';
                this.loading = false; // Desactivar estado de carga en caso de error
            }
        }
    },
     // Opcional: Si el usuario ya está logueado (token en store), redirigir desde aquí
     // mounted() {
     //    if (this.$store.getters.isAuthenticated) {
     //        this.$router.push('/products');
     //    }
     // }
};
</script>

<style scoped>
/* Estilos específicos para este componente */
</style>