<?php
namespace Rutas;

use Lib\Enrutador;
use Controladores\HomeControlador;
use Controladores\AuthControlador;
use Controladores\ProductoControlador;
use Controladores\CestaControlador;
use Controladores\PagoControlador;
use Controladores\AdminControlador;


class Rutas
{
    public static function registrar(): void
    {
    
        Enrutador::agregar('GET', '/', function () {
            (new HomeControlador())->index();
        });

    
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

        Enrutador::agregar('GET', '/pago/exito', function () {
            (new PagoControlador())->exito();
        });
        Enrutador::agregar('GET', '/pago/cancelado', function () {
            (new PagoControlador())->error();
        });
        Enrutador::agregar('GET', '/pago/gracias', function () {
            (new PagoControlador())->gracias();
        });
        Enrutador::agregar('GET', '/pago/error', function () {
            (new PagoControlador())->error();
        });

        Enrutador::agregar('GET', '/admin', function () {
            (new AdminControlador())->index();
        });


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

        
        Enrutador::agregar('GET', '/admin/categorias', function () {
            (new AdminControlador())->categorias();
        });
        Enrutador::agregar('POST', '/admin/guardarCategoria', function () {
            (new AdminControlador())->guardarCategoria();
        });
        Enrutador::agregar('GET', '/admin/borrarCategoria/:id', function ($id) {
            (new AdminControlador())->borrarCategoria($id);
        });

        
        Enrutador::agregar('GET', '/admin/usuarios', function () {
            (new AdminControlador())->usuarios();
        });
        Enrutador::agregar('GET', '/admin/borrarUsuario/:id', function ($id) {
            (new AdminControlador())->borrarUsuario($id);
        });


        Enrutador::agregar('GET', '/auth/loginGoogle', function () {
            (new AuthControlador())->loginGoogle();
        });

        Enrutador::agregar('GET', '/auth/googleCallback', function () {
            (new AuthControlador())->googleCallback();
        });

        Enrutador::agregar('GET', '/admin/restaurarProducto/:id', function ($id) {
            (new AdminControlador())->restaurarProducto($id);
        });

        Enrutador::agregar('GET', '/admin/editarCategoria/:id', function ($id) {
            (new AdminControlador())->editarCategoria($id);
        });

        Enrutador::agregar('GET', '/auth/olvideClave', function () {
            (new AuthControlador())->olvideClave();
        });
        Enrutador::agregar('POST', '/auth/procesarOlvideClave', function () {
            (new AuthControlador())->procesarOlvideClave();
        });
        Enrutador::agregar('GET', '/auth/resetPassword/:token', function ($token) {
            (new AuthControlador())->resetPassword($token);
        });
        Enrutador::agregar('POST', '/auth/procesarResetPassword', function () {
            (new AuthControlador())->procesarResetPassword();
        });

        Enrutador::despachar();
    }
}
