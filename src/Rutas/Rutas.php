<?php
/**
 * Definicion centralizada de rutas de la aplicacion.
 *
 * Se basa en la clase Enrutador (src/Lib/Enrutador.php).
 * Formato: Enrutador::agregar('METODO', '/ruta/', funcion_controlador);
 */
class Rutas
{
    public static function registrar(): void
    {
        // -------------------------------------------------------
        //  INICIO
        // -------------------------------------------------------
        Enrutador::agregar('GET', '/', function () {
            (new HomeControlador())->index();
        });

        // -------------------------------------------------------
        //  AUTENTICACION
        // -------------------------------------------------------
        Enrutador::agregar('GET', '/auth/registro', function () {
            (new AuthControlador())->registro();
        });
        Enrutador::agregar('POST', '/auth/procesarRegistro', function () {
            (new AuthControlador())->procesarRegistro();
        });
        Enrutador::agregar('GET', '/auth/confirmar/:id', function ($token) {
            (new AuthControlador())->confirmar($token);
        });
        Enrutador::agregar('GET', '/auth/login', function () {
            (new AuthControlador())->login();
        });
        Enrutador::agregar('POST', '/auth/procesarLogin', function () {
            (new AuthControlador())->procesarLogin();
        });
        Enrutador::agregar('GET', '/auth/logout', function () {
            (new AuthControlador())->logout();
        });

        // -------------------------------------------------------
        //  PRODUCTOS (catalogo publico)
        // -------------------------------------------------------
        Enrutador::agregar('GET', '/producto', function () {
            (new ProductoControlador())->index();
        });
        Enrutador::agregar('GET', '/producto/index/:id', function ($idCategoria) {
            (new ProductoControlador())->index($idCategoria);
        });
        Enrutador::agregar('GET', '/producto/detalle/:id', function ($id) {
            (new ProductoControlador())->detalle($id);
        });
        Enrutador::agregar('GET', '/producto/buscar', function () {
            (new ProductoControlador())->buscar();
        });

        // -------------------------------------------------------
        //  CESTA DE LA COMPRA
        // -------------------------------------------------------
        Enrutador::agregar('GET', '/cesta', function () {
            (new CestaControlador())->index();
        });
        Enrutador::agregar('POST', '/cesta/anadir', function () {
            (new CestaControlador())->anadir();
        });
        Enrutador::agregar('POST', '/cesta/actualizar', function () {
            (new CestaControlador())->actualizar();
        });
        Enrutador::agregar('GET', '/cesta/quitar/:id', function ($id) {
            (new CestaControlador())->quitar($id);
        });
        Enrutador::agregar('GET', '/cesta/vaciar', function () {
            (new CestaControlador())->vaciar();
        });
        Enrutador::agregar('GET', '/cesta/finalizar', function () {
            (new CestaControlador())->finalizar();
        });
        Enrutador::agregar('POST', '/cesta/confirmar', function () {
            (new CestaControlador())->confirmar();
        });

        // -------------------------------------------------------
        //  PANEL DE ADMINISTRACION
        // -------------------------------------------------------
        Enrutador::agregar('GET', '/admin', function () {
            (new AdminControlador())->index();
        });

        // -- Productos (admin) --
        Enrutador::agregar('GET', '/admin/productos', function () {
            (new AdminControlador())->productos();
        });
        Enrutador::agregar('GET', '/admin/nuevoProducto', function () {
            (new AdminControlador())->nuevoProducto();
        });
        Enrutador::agregar('GET', '/admin/editarProducto/:id', function ($id) {
            (new AdminControlador())->editarProducto($id);
        });
        Enrutador::agregar('POST', '/admin/guardarProducto', function () {
            (new AdminControlador())->guardarProducto();
        });
        Enrutador::agregar('GET', '/admin/borrarProducto/:id', function ($id) {
            (new AdminControlador())->borrarProducto($id);
        });

        // -- Categorias (admin) --
        Enrutador::agregar('GET', '/admin/categorias', function () {
            (new AdminControlador())->categorias();
        });
        Enrutador::agregar('POST', '/admin/guardarCategoria', function () {
            (new AdminControlador())->guardarCategoria();
        });
        Enrutador::agregar('GET', '/admin/borrarCategoria/:id', function ($id) {
            (new AdminControlador())->borrarCategoria($id);
        });

        // -- Usuarios (admin) --
        Enrutador::agregar('GET', '/admin/usuarios', function () {
            (new AdminControlador())->usuarios();
        });
        Enrutador::agregar('GET', '/admin/borrarUsuario/:id', function ($id) {
            (new AdminControlador())->borrarUsuario($id);
        });

        // -------------------------------------------------------
//  GOOGLE AUTH
// -------------------------------------------------------
Enrutador::agregar('GET', '/auth/loginGoogle', function () {
    (new AuthControlador())->loginGoogle();
});

Enrutador::agregar('GET', '/auth/googleCallback', function () {
    (new AuthControlador())->googleCallback();
});

        // -------------------------------------------------------
        //  Despachar la peticion actual
        // -------------------------------------------------------
        Enrutador::despachar();
    }
}
