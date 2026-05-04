# Tienda PHP — Proyecto 2 DAW

Tienda online desarrollada en PHP nativo siguiendo una arquitectura MVC propia,
sin frameworks. Incluye catálogo de productos, carrito de compra, pasarela PayPal,
autenticación con Google OAuth y panel de administración completo.

---

## Características

- Catálogo de productos con filtro por categorías y buscador.
- Registro de usuarios con email de confirmación (enviado vía PHPMailer / Mailtrap).
- Login y logout con sesiones y contraseñas hasheadas (bcrypt).
- Login alternativo mediante **Google OAuth 2.0**.
- Cesta de la compra almacenada en sesión.
- Finalización de compra con integración de **PayPal** (sandbox).
- Panel de administración con CRUD de productos, categorías y listado de usuarios.
- Enrutador propio con soporte de parámetros dinámicos (`:id`).
- Capa de Servicios y Repositorios separada de los Controladores.
- Paginación en el catálogo de productos.
- Subida de imágenes de producto a `public/uploads/imagenes/`.
- Sin frameworks: PHP nativo + PDO + Composer + CSS propio.

---

## Requisitos

- **XAMPP** con Apache y MySQL/MariaDB (PHP 8.0+ recomendado).
- Módulo `mod_rewrite` activo (XAMPP lo incluye por defecto).
- **Composer** instalado globalmente (o ejecutado desde la carpeta del proyecto).

---

## Instalación paso a paso

### 1. Copiar el proyecto

Coloca la carpeta en `C:\xampp\htdocs\ProyectoTiendaPHP`

### 2. Instalar dependencias Composer

```bash
cd C:\xampp\htdocs\ProyectoTiendaPHP
composer install
```

Esto instalará **PHPMailer** y el **SDK de PayPal**.

### 3. Configurar el archivo `.env`

Copia o edita el fichero `.env` de la raíz y rellena tus datos:

```env
# Base de datos
DB_HOST=localhost
DB_NAME=tiendaphp
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4

# URL base del proyecto
BASE_URL=/ProyectoTiendaPHP

# SMTP — usa Mailtrap para pruebas (https://mailtrap.io)
SMTP_HOST=sandbox.smtp.mailtrap.io
SMTP_PORT=2525
SMTP_USER=tu_usuario_mailtrap
SMTP_PASS=tu_password_mailtrap
SMTP_FROM=no-responder@tiendaphp.local
SMTP_FROM_NAME=Tienda PHP

# PayPal Sandbox (https://developer.paypal.com)
PAYPAL_CLIENT_ID=tu_client_id
PAYPAL_SECRET=tu_secret
```

### 4. Crear la base de datos

Abre phpMyAdmin (`http://localhost/phpmyadmin`), crea una base de datos llamada
`tiendaphp` e importa el fichero:

```
miSQL/tiendaphp.sql
```

Se crearán todas las tablas y datos de ejemplo.

### 5. Insertar usuarios de prueba

Visita esta URL **una sola vez**:

```
http://localhost/ProyectoTiendaPHP/public/instalar.php
```

Crea dos usuarios con contraseñas correctamente hasheadas:

| Email                  | Contraseña   | Rol     |
|------------------------|--------------|---------|
| admin@tiendaphp.com    | admin123     | admin   |
| cliente@tiendaphp.com  | cliente123   | cliente |

> ⚠️ Tras ejecutarlo, **borra o renombra** `public/instalar.php`.

### 6. Acceder a la tienda

```
http://localhost/ProyectoTiendaPHP/
```

Si el `.htaccess` de la raíz no funciona, accede directamente a:

```
http://localhost/ProyectoTiendaPHP/public/
```

---

## Estructura del proyecto

```
ProyectoTiendaPHP/
├── config/
│   ├── Conexion.php          Conexión PDO singleton
│   └── google.php            Configuración Google OAuth
├── miSQL/
│   └── tiendaphp.sql         Esquema y datos de ejemplo
├── public/
│   ├── css/
│   │   └── estilo.css        Estilos propios
│   ├── img/                  Imágenes estáticas de ejemplo
│   ├── uploads/imagenes/     Imágenes subidas por el admin
│   ├── .htaccess             Front controller Apache
│   ├── index.php             Punto de entrada de la aplicación
│   └── instalar.php          Script de instalación (borrar tras usar)
├── src/
│   ├── Controladores/        Lógica de cada sección
│   │   ├── AdminControlador.php
│   │   ├── AuthControlador.php
│   │   ├── CestaControlador.php
│   │   ├── HomeControlador.php
│   │   ├── PagoControlador.php
│   │   └── ProductoControlador.php
│   ├── Lib/                  Clases de infraestructura
│   │   ├── Cesta.php         Gestión del carrito en sesión
│   │   ├── Enrutador.php     Router con parámetros dinámicos
│   │   ├── EnvioMail.php     Envío de emails con PHPMailer
│   │   ├── GoogleOAuth.php   Autenticación con Google
│   │   └── Sesion.php        Gestión de sesiones y mensajes flash
│   ├── Middleware/
│   │   ├── AccesoMiddleware.php  Requiere login
│   │   └── AdminMiddleware.php   Requiere rol admin
│   ├── Modelos/              Entidades de datos
│   │   ├── Categoria.php
│   │   ├── Pedido.php
│   │   ├── Producto.php
│   │   └── Usuario.php
│   ├── Repositorios/         Acceso a base de datos (PDO)
│   │   ├── CategoriaRepositorio.php
│   │   ├── PedidoRepositorio.php
│   │   ├── ProductoRepositorio.php
│   │   └── UsuarioRepositorio.php
│   ├── Requests/             Validación de formularios
│   │   ├── CategoriaRequest.php
│   │   ├── LoginRequest.php
│   │   ├── ProductoRequest.php
│   │   └── RegistroRequest.php
│   ├── Rutas/
│   │   └── Rutas.php         Registro de todas las rutas
│   ├── Servicios/            Lógica de negocio
│   │   ├── CategoriaServicio.php
│   │   ├── PedidoServicio.php
│   │   ├── ProductoServicio.php
│   │   └── UsuarioServicio.php
│   ├── Utils/
│   │   ├── Paginador.php     Paginación del catálogo
│   │   ├── Paypal.php        Integración SDK PayPal
│   │   └── Utilidades.php    Funciones auxiliares
│   ├── Vistas/               Plantillas PHP
│   │   ├── admin/
│   │   ├── auth/
│   │   ├── cesta/
│   │   ├── comunes/          Cabecera y pie de página
│   │   ├── errores/
│   │   ├── home/
│   │   ├── pago/
│   │   └── productos/
│   └── init.php              Bootstrap: autoload, .env, rutas
├── vendor/                   Dependencias Composer (no subir a git)
├── .env                      Variables de entorno (no subir a git)
├── .gitignore
├── composer.json
└── README.md
```

---

## Rutas disponibles

| Método | URL                              | Controlador / Acción                     |
|--------|----------------------------------|------------------------------------------|
| GET    | `/`                              | `HomeControlador::index()`               |
| GET    | `/producto`                      | `ProductoControlador::index()`           |
| GET    | `/producto/index/:id`            | Catálogo filtrado por categoría          |
| GET    | `/producto/detalle/:id`          | Detalle de un producto                   |
| GET    | `/producto/buscar?q=...`         | Resultados de búsqueda                   |
| GET    | `/auth/registro`                 | Formulario de registro                   |
| POST   | `/auth/procesarRegistro`         | Procesar registro                        |
| GET    | `/auth/confirmar/:token`         | Activar cuenta por email                 |
| GET    | `/auth/login`                    | Formulario de login                      |
| POST   | `/auth/procesarLogin`            | Procesar login                           |
| GET    | `/auth/logout`                   | Cerrar sesión                            |
| GET    | `/auth/loginGoogle`              | Iniciar OAuth con Google                 |
| GET    | `/auth/googleCallback`           | Callback de Google OAuth                 |
| GET    | `/cesta`                         | Ver cesta                                |
| POST   | `/cesta/anadir`                  | Añadir producto a la cesta               |
| POST   | `/cesta/actualizar`              | Actualizar cantidades                    |
| GET    | `/cesta/quitar/:id`              | Quitar un producto                       |
| GET    | `/cesta/vaciar`                  | Vaciar la cesta                          |
| GET    | `/cesta/finalizar`               | Pantalla resumen antes de pagar          |
| POST   | `/cesta/confirmar`               | Lanzar pago con PayPal                   |
| GET    | `/pago/exito`                    | PayPal confirma el pago                  |
| GET    | `/pago/cancelado`                | El usuario cancela en PayPal             |
| GET    | `/pago/gracias`                  | Página de pedido completado              |
| GET    | `/pago/error`                    | Error en el proceso de pago              |
| GET    | `/admin`                         | Panel de administración                  |
| GET    | `/admin/productos`               | Listado de productos                     |
| GET    | `/admin/nuevoProducto`           | Formulario nuevo producto                |
| GET    | `/admin/editarProducto/:id`      | Formulario editar producto               |
| POST   | `/admin/guardarProducto`         | Guardar producto (nuevo o editado)       |
| GET    | `/admin/borrarProducto/:id`      | Eliminar producto                        |
| GET    | `/admin/categorias`              | Listado de categorías                    |
| POST   | `/admin/guardarCategoria`        | Guardar categoría                        |
| GET    | `/admin/borrarCategoria/:id`     | Eliminar categoría                       |
| GET    | `/admin/usuarios`                | Listado de usuarios                      |
| GET    | `/admin/borrarUsuario/:id`       | Eliminar usuario                         |

---

## Email de confirmación

Los emails se envían a través de **PHPMailer** usando las credenciales SMTP del
fichero `.env`. Para pruebas se recomienda usar [Mailtrap](https://mailtrap.io),
que intercepta los correos sin enviarlos de verdad. Solo hay que copiar los datos
de conexión que Mailtrap te proporciona en la sección *SMTP Settings*.

---

## Integración PayPal

Se usa el **PayPal Server SDK** (sandbox). Para configurarlo:

1. Crea una app en [https://developer.paypal.com](https://developer.paypal.com).
2. Copia el `Client ID` y el `Secret` de la app sandbox.
3. Pégalos en las variables `PAYPAL_CLIENT_ID` y `PAYPAL_SECRET` del `.env`.

El flujo es: cesta → confirmar → PayPal → `pago/exito` → pedido creado en BD.

---

## Login con Google

1. Crea un proyecto en [Google Cloud Console](https://console.cloud.google.com).
2. Habilita la API *OAuth 2.0* y crea credenciales de tipo *Web application*.
3. Añade `http://localhost/ProyectoTiendaPHP/auth/googleCallback` como URI de
   redirección autorizada.
4. Rellena los datos en `config/google.php`.

---

## Dependencias (Composer)

| Paquete                       | Uso                          |
|-------------------------------|------------------------------|
| `phpmailer/phpmailer ^7.0`    | Envío de emails SMTP         |
| `paypal/paypal-server-sdk ^2.2` | Integración PayPal         |
