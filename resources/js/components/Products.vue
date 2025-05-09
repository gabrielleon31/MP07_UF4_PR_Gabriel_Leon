<template>
  <v-container>
    <v-btn
      v-if="isAdmin"
      @click="openAddDialog"
      color="primary"
      class="mb-4"
    >
      Añadir Producto
    </v-btn>

    <v-dialog v-model="showDialog" max-width="600">
      <v-card>
        <v-card-title>{{ editingId ? 'Editar Producto' : 'Añadir Nuevo Producto' }}</v-card-title>
        <v-card-text>
          <v-form @submit.prevent="saveProduct">
            <v-text-field v-model="newProduct.name" label="Nombre" required outlined dense></v-text-field>
            <v-text-field v-model="newProduct.description" label="Descripción" outlined dense></v-text-field>
            <v-text-field v-model="newProduct.price" label="Precio" type="number" required outlined dense></v-text-field>

            <v-card-actions>
               <v-spacer></v-spacer>
               <v-btn color="blue darken-1" text @click="closeDialog">Cancelar</v-btn>
               <v-btn color="blue darken-1" text type="submit">Guardar</v-btn>
            </v-card-actions>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-alert v-if="fetchError" type="error" dense text class="mt-3">
       {{ fetchError }}
     </v-alert>

     <v-table fixed-header height="400px"> <thead>
        <tr>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Precio</th>
          <th v-if="isAdmin">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="product in products" :key="product.id">
          <td>{{ product.name }}</td>
          <td>{{ product.description }}</td>
          <td>{{ product.price }} €</td>
          <td v-if="isAdmin">
            <v-btn icon small class="mr-2" @click="editProduct(product)">
              <v-icon small>mdi-pencil</v-icon>
            </v-btn>
            <v-btn icon small @click="deleteProduct(product.id)">
              <v-icon small>mdi-delete</v-icon>
            </v-btn>
          </td>
        </tr>
         <tr v-if="products.length === 0 && !fetchError">
             <td colspan="4" class="text-center">No hay productos disponibles.</td>
         </tr>
      </tbody>
    </v-table>

    <v-btn
      v-if="isAuthenticated"
      @click="logout"
      color="red"
      class="mt-4"
    >
      Logout
    </v-btn>

  </v-container>
</template>

<script>
// No necesitas importar axios aquí si ya está configurado globalmente en bootstrap.js y inyectado via app.config.globalProperties.$axios o si usas las acciones del store para las llamadas.

export default {
  data() {
    return {
      products: [], // Array que contendrá la lista de productos
      showDialog: false, // Controla la visibilidad del diálogo (añadir/editar)
      newProduct: { // Objeto para el formulario de añadir/editar
        name: '',
        description: '',
        price: null // Usar null o 0 inicialmente
      },
      editingId: null, // Guarda el ID del producto que se está editando (si es null, es añadir)
      fetchError: '', // Mensaje de error para la carga inicial de productos
      // loadingProducts: false // Opcional: para estado de carga de la tabla
    }
  },
  // Propiedades computadas que leen del Vuex store
  computed: {
    isAuthenticated() {
      // Accede al getter 'isAuthenticated' del store para saber si el usuario está logueado
      return this.$store.getters.isAuthenticated;
    },
    user() {
      // Accede al getter 'getUser' del store para obtener el objeto usuario
      return this.$store.getters.getUser;
    },
    isAdmin() {
       // Accede al getter 'isAdmin' del store para saber si el usuario logueado es admin
       return this.$store.getters.isAdmin;
    }
  },
  // Se ejecuta cuando el componente ha sido montado en el DOM
  async mounted() {
    // Cargar los productos al inicio, solo si está autenticado (opcional, puedes permitir ver lista sin autenticar si la API lo permite)
     if (this.isAuthenticated) { // Opcional: solo cargar si está logueado
       await this.fetchProducts();
     } else {
       // Si la ruta /products requiere autenticación (definido en el router guard),
       // el usuario será redirigido a /login por el router guard antes de montar este componente.
       // Si la ruta /products permite invitados (pero la API Get /api/products requiere auth),
       // la llamada a fetchProducts fallará si no hay token, y el mensaje de error fetchError se mostrará.
       // Para este proyecto, la API GET /api/products sí requiere autenticación,
       // por lo que la comprobación isAuthenticated aquí es redundante si el router guard está bien configurado.
        await this.fetchProducts(); // Llamar siempre, el API fallará si no hay auth
     }
  },
  methods: {
    // Método para obtener la lista de productos de la API
    async fetchProducts() {
      // this.loadingProducts = true; // Activar carga (opcional)
      this.fetchError = ''; // Limpiar errores anteriores
      try {
        // Llamada Axios para obtener productos. Usa '/products' porque baseURL es '/api'.
        // La cabecera Authorization se añade automáticamente si el token está en el store y se configuró en SET_TOKEN
        const response = await this.$axios.get('/products');

        // **Importante:** Ajusta esto según la estructura REAL de la respuesta de tu API GET /api/products.
        // Si la respuesta es { data: [...] }, usa response.data.data.
        // Si la respuesta es directamente un array [...], usa response.data.
        if (response.data && Array.isArray(response.data.data)) {
            this.products = response.data.data; // Para API Laravel Resources
        } else if (Array.isArray(response.data)) {
             this.products = response.data; // Para un array directo
        } else {
            console.error('Error: Estructura de datos de la API inesperada al cargar productos:', response.data);
            this.fetchError = 'Error al cargar los productos. Estructura de datos inesperada.';
            this.products = []; // Limpiar lista
        }

      } catch (error) {
        console.error('Error fetching products:', error.response || error);
        // Mostrar un mensaje de error si la carga falla (ej: 401 si el token expira y no se maneja)
        this.fetchError = 'Error al cargar los productos. Inténtalo de nuevo.';
        this.products = []; // Vaciar la lista
      } finally {
        // this.loadingProducts = false; // Desactivar carga (opcional)
      }
    },

    // Método para abrir el diálogo en modo añadir
    openAddDialog() {
        this.resetForm(); // Limpiar el formulario
        this.editingId = null; // Asegurarse de que es modo añadir
        this.showDialog = true; // Mostrar el diálogo
    },

    // Método para cerrar el diálogo y resetear el formulario
    closeDialog() {
        this.showDialog = false; // Ocultar el diálogo
        this.resetForm(); // Limpiar formulario
    },

    // Método para guardar (añadir o editar) un producto
    async saveProduct() {
      // Añadir validación básica de campos si es necesario
      if (!this.newProduct.name || this.newProduct.price === null || this.newProduct.price === '') {
          alert('El nombre y el precio son obligatorios.'); // Alerta básica de validación
          return; // Detener si falla
      }

      try {
        // Construir el objeto de datos a enviar
        const productData = {
             name: this.newProduct.name,
             description: this.newProduct.description || '', // Enviar descripción (cadena vacía si no hay)
             price: parseFloat(this.newProduct.price) // Asegurar que el precio es un número
        };

        let response;
        if (this.editingId) {
          // Si editingId tiene valor, es edición (PUT a /api/products/{id})
          response = await this.$axios.put(`/products/${this.editingId}`, productData);
          console.log('Producto actualizado:', response.data);
        } else {
          // Si editingId es null, es añadir (POST a /api/products)
          response = await this.$axios.post('/products', productData);
          console.log('Producto creado:', response.data);
        }

        this.closeDialog(); // Cerrar el diálogo después de guardar
        await this.fetchProducts(); // Recargar la lista para ver los cambios

      } catch (error) {
        console.error('Error saving product:', error.response || error);
        const errorMessage = error.response?.data?.message || error.message || 'Error al guardar el producto.';
        alert('Error: ' + errorMessage); // Mostrar un alert simple si falla el guardado
      }
    },

    // Método para preparar el formulario para editar un producto
    editProduct(product) {
      // Copiar los datos del producto al formulario para editar
      // Usamos {...product} para una copia superficial, suficiente aquí
      this.newProduct = { ...product };
      this.editingId = product.id; // Guardar el ID
      this.showDialog = true; // Mostrar el diálogo
    },

    // Método para eliminar un producto
    async deleteProduct(id) {
      // Pedir confirmación
      if (confirm('¿Estás seguro de eliminar este producto? Esta acción es irreversible.')) {
        try {
          // Llamada DELETE a /api/products/{id}
          await this.$axios.delete(`/products/${id}`);
          console.log('Producto eliminado ID:', id);
          // Recargar la lista después de eliminar
          await this.fetchProducts();
        } catch (error) {
          console.error('Error deleting product:', error.response || error);
           const errorMessage = error.response?.data?.message || error.message || 'Error al eliminar el producto.';
           alert('Error: ' + errorMessage); // Mostrar error si falla la eliminación
        }
      }
    },

    // Método para resetear el formulario (usado al añadir o cancelar edición)
    resetForm() {
      this.newProduct = {
        name: '',
        description: '',
        price: null
      };
      this.editingId = null; // Asegurarse de que no estamos en modo edición
    },

    // Método para hacer logout (llama a la acción del store)
    logout() {
      // Llama a la acción 'logout' del Vuex store
      this.$store.dispatch('logout').then(() => {
         // Opcional: redirigir a la página de login después del logout
         this.$router.push('/login');
      });
    }
  }
}
</script>

<style scoped>
/* Estilos específicos para este componente */
/* Puedes añadir estilos para la tabla, botones, etc. aquí */
</style>