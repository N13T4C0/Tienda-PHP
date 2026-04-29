# Proyecto Tienda PHP (2DAW)

Tienda online sencilla en PHP siguiendo el patron MVC. Inspirada en
[Tienda-PHP de pablovacasgarcia](https://github.com/pablovacasgarcia/TiendaPHP)
pero reescrita con nombres y estructura propios para facilitar su comprension
en 2º DAW.

## Caracteristicas

- Catalogo de productos con filtro por categorias y buscador.
- Registro de usuarios (con email de confirmacion guardado en disco).
- Login y logout con sesiones y contrasenas hasheadas (bcrypt).
- Cesta de la compra (almacenada en sesion).
- Finalizar compra: crea pedido + lineas en transaccion y descuenta stock.
- Panel de administracion con CRUD de productos y categorias y listado de usuarios.
- Sin frameworks: solo PHP nativo + PDO + CSS propio.

## Requisitos

- XAMPP con Apache y MySQL/MariaDB (PHP 7.4+ recomendado, ideal 8.0+).
- Modulo `mod_rewrite` activo (XAMPP lo trae por defecto).

## Instalacion paso a paso

1. **Copiar el proyecto** dentro de `C:\xampp\htdocs\ProyectoTiendaPHP`
   (ya esta ahi si estas leyendo esto).

2. **Crear la base de datos**: abre phpMyAdmin
   (`http://localhost/phpmyadmin`) y desde la pestana *Importar* sube el
   fichero `sql/tiendaphp.sql`. Se creara la BD `tiendaphp` con sus tablas
   y datos de ejemplo.

3. **Instalar usuarios de prueba**: visita una sola vez:

   ```
   http://localhost/ProyectoTiendaPHP/public/instalar.php
   ```

   Esto creara dos usuarios con sus claves correctamente hasheadas:

   | Email                    | Clave        | Rol     |
   |--------------------------|--------------|---------|
   | admin@tiendaphp.com      | admin123     | admin   |
   | cliente@tiendaphp.com    | cliente123   | cliente |

   Tras ejecutarlo, **borra o renombra** `public/instalar.php`.

4. **Acceder a la tienda**:

   ```
   http://localhost/ProyectoTiendaPHP/
   ```

   Si por algun motivo el `.htaccess` de la raiz no funciona, tambien puedes
   entrar directamente en `http://localhost/ProyectoTiendaPHP/public/` (en
   ese caso cambia `URL_BASE` en `public/index.php` por
   `'/ProyectoTiendaPHP/public'`).

## Estructura

```
ProyectoTiendaPHP/
├── app/
│   ├── ayudantes/        Helpers (Sesion, Cesta, EnvioMail)
│   ├── config/           Conexion PDO
│   ├── controladores/    Logica de cada seccion
│   ├── modelos/          Acceso a BD
│   └── vistas/           Plantillas .php
├── public/               Document root publico
│   ├── css/
│   ├── img/
│   ├── .htaccess         Reescritura de URLs
│   ├── index.php         Front controller
│   └── instalar.php      Script de instalacion (borrar tras usar)
├── sql/
│   └── tiendaphp.sql     Esquema y datos de ejemplo
├── storage/
│   └── mails/            Emails simulados (HTML por archivo)
├── .htaccess             Redirige raiz a /public/
└── README.md
```

## Convenciones de nombres

| Concepto        | Nombre     | Metodos principales                     |
|-----------------|------------|------------------------------------------|
| Producto        | `Producto` | `listar`, `obtenerUno`, `guardar`, ...   |
| Categoria       | `Categoria`| `listar`, `tieneProductos`, `guardar`    |
| Usuario         | `Usuario`  | `registrar`, `buscarPorEmail`, `activarCuenta` |
| Pedido          | `Pedido`   | `crearPedidoCompleto`, `listarPorUsuario`|
| Cesta (helper)  | `Cesta`    | `meterProducto`, `cambiarUnidades`, `vaciar` |
| Sesion (helper) | `Sesion`   | `iniciar`, `cerrar`, `exigirAdmin`, `mensaje` |

## Sistema de URLs

El router por convencion lee la ruta como `controlador/accion/parametros`:

| URL                                    | Hace                                   |
|----------------------------------------|----------------------------------------|
| `/`                                    | `HomeControlador::index()`             |
| `/producto`                            | `ProductoControlador::index()`         |
| `/producto/index/3`                    | Catalogo filtrado por categoria 3      |
| `/producto/detalle/12`                 | Detalle del producto 12                |
| `/producto/buscar?q=raton`             | Resultados de busqueda                 |
| `/auth/login` / `/auth/registro`       | Formularios                            |
| `/auth/procesarLogin` / `procesarRegistro` | Procesado POST                     |
| `/auth/confirmar/{token}`              | Activacion de cuenta                   |
| `/cesta`                               | Ver cesta                              |
| `/cesta/anadir` (POST)                 | Anadir producto                        |
| `/cesta/finalizar`                     | Pedir direccion de envio               |
| `/cesta/confirmar` (POST)              | Crear el pedido                        |
| `/admin`                               | Panel admin                            |
| `/admin/productos` `/categorias` `/usuarios` | CRUDs                            |

## Email de confirmacion

Por simplicidad, los emails NO se envian de verdad: el helper
`EnvioMail` esta en **modo simulacion** y guarda cada correo como un
archivo `.html` en `storage/mails/`. Asi se puede ver el resultado sin
configurar SMTP. Cambiar `MODO_SIMULACION` a `false` en
`app/ayudantes/EnvioMail.php` para usar la funcion `mail()` real.

## Notas para el alumnado de 2DAW

- Si XAMPP esta en otra carpeta o usas otra ruta, ajusta la constante
  `URL_BASE` en `public/index.php`.
- Para anadir nuevos controladores basta con crear un archivo en
  `app/controladores/NombreControlador.php` con clase `NombreControlador`.
- Las vistas son simples ficheros PHP; en el controlador se hace
  `require` de cabecera, vista y pie.
- Para anadir imagenes reales de productos, copialas en `public/img/`
  y pon el nombre del archivo en el panel de admin al crear/editar el producto.
