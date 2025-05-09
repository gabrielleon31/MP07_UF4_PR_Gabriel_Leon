# 🌐 Gestor de Productos con Autenticación y Interfaz Dinámica

Proyecto desarrollado como parte de la práctica de la **UF4 del Módulo MP07 - Desarrollo Web en Entorno Servidor**, centrado en la creación de una API RESTful con Laravel y una interfaz web dinámica con Laravel Blade y Vue.js.

---

## ✨ Características Principales

Este proyecto implementa:

* **API RESTful con Laravel:** Gestión de productos con endpoints protegidos.
* **Autenticación Segura:** Utilización de **Laravel Sanctum** para el registro y login de usuarios vía API.
* **Control de Acceso por Roles:** Definición de dos roles (**Admin** y **Usuario Normal**), con permisos diferenciados para las acciones sobre productos (ver, crear, editar, eliminar).
* **Interfaz Web Dinámica:** Construida con **Laravel Blade** y **Vue.js**, interactuando con la API para mostrar y gestionar productos.
* **Gestión de Productos en Frontend:** Visualización de productos en una tabla dinámica. Funcionalidades de añadir, editar y eliminar productos visibles solo para usuarios con rol Admin.
* **Persistencia de Sesión:** Mantenimiento del estado de autenticación en el frontend utilizando tokens (gestionados con Vuex y localStorage).

---

## 🚀 Tecnologías Utilizadas

* **Backend:**
    * PHP 8.x
    * Laravel 10.x
    * Laravel Sanctum
    * Eloquent ORM
    * API Resources
    * MySQL (Base de Datos)
* **Frontend:**
    * Vue.js 3
    * Vue Router
    * Vuex (Gestión de Estado de Autenticación)
    * Axios (Cliente HTTP)
    * Vuetify (Framework de Componentes UI)
    * Tailwind CSS
    * Vite (Bundler de Assets)
    * Laravel Blade

* **Herramientas Adicionales:**
    * Composer (Gestor de Dependencias PHP)
    * npm (Gestor de Paquetes Node.js)
    * Git (Control de Versiones)
    * Talend API Tester / Postman (Para probar la API directamente)
    * XAMPP / Laragon (Entorno de Desarrollo Local con Servidor Web y MySQL)

---

## 📋 Requisitos del Sistema

Antes de clonar y ejecutar este proyecto, asegúrate de tener instalado:

* PHP >= 8.1
* Composer
* Node.js >= 16
* npm >= 8
* Git
* Un servidor de base de datos MySQL (incluido en XAMPP, Laragon, etc.)

---

## 🔧 Instalación y Configuración

Sigue estos pasos para poner el proyecto en marcha en tu entorno local:

1.  **Clona el repositorio:**
    ```bash
    git clone <URL_DE_TU_REPOSITORIO_EN_GITHUB>
    ```
    Navega a la carpeta del proyecto:
    ```bash
    cd MP07_UF4_PF_Gabriel_Leon
    ```
    *(Asegúrate de estar en la carpeta raíz donde se encuentran `artisan`, `composer.json`, `package.json`, etc.)*

2.  **Instala las dependencias de PHP:**
    ```bash
    composer install
    ```

3.  **Instala las dependencias de JavaScript:**
    ```bash
    npm install
    ```

4.  **Crea el archivo de configuración `.env`:**
    ```bash
    copy .env.example .env   # En Windows (CMD)
    # o
    cp .env.example .env     # En Linux/macOS
    ```

5.  **Configura el archivo `.env`:**
    Abre el archivo `.env` con tu editor de texto. Configura al menos las opciones de conexión a tu base de datos MySQL:
    ```dotenv
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nombre_de_tu_base_de_datos_local # <-- Cámbialo
    DB_USERNAME=tu_usuario_de_base_de_datos     # <-- Cámbialo (ej: root)
    DB_PASSWORD=tu_password_de_base_de_datos     # <-- Cámbialo (ej: vacío en XAMPP por defecto)

    APP_URL=http://localhost:8000 # Si usas php artisan serve
    ```

6.  **Genera la clave de la aplicación:**
    ```bash
    php artisan key:generate
    ```

7.  **Ejecuta las migraciones de la base de datos y los seeders:**
    ```bash
    php artisan migrate --seed
    ```
    Este comando creará las tablas necesarias y poblará la base de datos con usuarios por defecto (Admin y Normal).
    * **Credenciales por defecto del Seeder:** Revisa el archivo `database/seeders/UserSeeder.php` (o archivos relacionados en la carpeta `database/seeders`) para encontrar las credenciales exactas (email y password) de los usuarios Admin y Normal creados por el seeder. Las necesitarás para hacer login y pruebas.

---

## ▶️ Ejecución del Proyecto

Necesitas dos terminales abiertas en la carpeta raíz del proyecto:

1.  **Inicia el servidor de desarrollo de Laravel (Backend API):**
    ```bash
    php artisan serve
    ```
    Esto iniciará el servidor que sirve la API RESTful y las vistas Blade base. La URL será típicamente `http://localhost:8000`.

2.  **Inicia el servidor de desarrollo de Vite (Frontend Assets):**
    ```bash
    npm run dev
    ```
    Esto iniciará el servidor de Vite que compila y sirve los archivos JavaScript y CSS del frontend (Vue.js, Vuetify, Tailwind).

3.  **Accede a la aplicación en el navegador:**
    Abre tu navegador web y ve a:
    ```
    http://localhost:8000
    ```
    Deberías ser redirigido a la página de login del frontend.

---

## 🧪 Pruebas de la API (Parte 1)

La API puede ser probada directamente usando herramientas como Talend API Tester o Postman.

* **URL Base:** `http://localhost:8000/api`
* **Endpoints Clave:**
    * `POST /login`: Autenticación de usuario (requiere `email`, `password` en el body JSON). Devuelve token y datos de usuario.
    * `GET /products`: Obtener lista de productos (requiere `Authorization: Bearer <token>` header). Accessible para todos los usuarios autenticados.
    * `POST /products`: Crear nuevo producto (requiere `Authorization: Bearer <token_admin>` header y datos del producto en el body JSON). Accesible solo para Admin.
    * `PUT /products/{id}`: Actualizar producto por ID (requiere `Authorization: Bearer <token_admin>` header y datos actualizados en el body JSON). Accesible solo para Admin.
    * `DELETE /products/{id}`: Eliminar producto por ID (requiere `Authorization: Bearer <token_admin>` header). Accesible solo para Admin.

* **Validación:** Los endpoints `POST` y `PUT` sobre productos implementan validación (Form Requests). Enviar datos inválidos debería resultar en una respuesta `422 Unprocessable Entity`.
* **Roles:** Prueba las rutas `POST`, `PUT`, `DELETE` con el token de un usuario Normal para verificar que devuelven `403 Forbidden`.

---

## 🌐 Pruebas de la Interfaz Web (Parte 2)

Con ambos servidores (`php artisan serve` y `npm run dev`) corriendo, prueba la interfaz en tu navegador:

1.  **Página de Login:**
    * Accede a `http://localhost:8000`. Deberías ver el formulario de login.
    * Introduce las credenciales correctas de Admin y haz login. Deberías ser redirigido a la página de productos.
    * Introduce las credenciales correctas de Usuario Normal y haz login. Deberías ser redirigido a la página de productos.
    * Introduce credenciales incorrectas. Deberías ver un mensaje de error en el formulario.
    * **Persistencia de Sesión:** Después de loguearte, refresca la página (`F5` o `Ctrl+R`). La sesión debería mantenerse y no deberías ser redirigido a login (gracias a la gestión de estado con Vuex y localStorage).
2.  **Página de Productos (`/products`):**
    * **Visualización:** Deberías ver la lista de productos en una tabla, obtenida de la API.
    * **Ordenación:** *(Nota: La ordenación al hacer clic en la cabecera no parece estar implementada en el código frontend proporcionado. Los productos podrían mostrarse ordenados por defecto del API, pero el requisito de ordenar *al hacer clic* no está cubierto.)*
    * **Control de Roles (Admin):** Si estás logueado como **Admin**, deberías ver el botón "Añadir Producto" y los botones de "Editar" y "Eliminar" en la columna "Acciones". Prueba a:
        * Añadir un nuevo producto (rellenando el formulario en el diálogo). Debería aparecer en la tabla dinámicamente.
        * Editar un producto existente (haciendo clic en el botón de lápiz). Debería abrir el diálogo con los datos precargados y actualizar la tabla al guardar.
        * Eliminar un producto (haciendo clic en el botón de papelera). Debería desaparecer de la tabla al confirmar.
        *(Nota: Los iconos en los botones de acción podrían no visualizarse correctamente debido a problemas de carga de la fuente de iconos MDI. Esto es un problema visual a depurar en la configuración del frontend/Vite/CSS.)*
    * **Control de Roles (Usuario Normal):** Si estás logueado como **Usuario Normal**, deberías ver la lista de productos, pero **NO** deberías ver el botón "Añadir Producto" ni los botones de "Editar" y "Eliminar" en la columna "Acciones".

---

## 🛠️ Problemas Conocidos / Pendientes

* La documentación de la API con Swagger/OpenAPI no está implementada o configurada.

---

## 👤 Autor

* Gabriel Leon
