<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Router;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

//iniciamos la sesion
session_start();

//creamos el router
$router = new Router();

//definimos las rutas del router
$router->add('GET', '/', 'App\Controllers\HomeController', 'index');
$router->add('GET', '/registro', 'App\Controllers\AuthController', 'registro');
$router->add('POST', '/registro', 'App\Controllers\AuthController', 'guardarRegistro');
$router->add('GET', '/login', 'App\Controllers\AuthController', 'login');
$router->add('POST', '/login', 'App\Controllers\AuthController', 'autenticar');
$router->add('GET', '/logout', 'App\Controllers\AuthController', 'logout');
// ====== RUTAS ADMIN ======
// Categorías
$router->add('GET', '/admin/categorias', 'App\Controllers\CategoriaController', 'index');
$router->add('GET', '/admin/categorias/crear', 'App\Controllers\CategoriaController', 'crear');
$router->add('POST', '/admin/categorias/guardar', 'App\Controllers\CategoriaController', 'guardar');
$router->add('GET', '/admin/categorias/editar/{id}', 'App\Controllers\CategoriaController', 'editar');
$router->add('POST', '/admin/categorias/actualizar/{id}', 'App\Controllers\CategoriaController', 'actualizar');
$router->add('POST', '/admin/categorias/eliminar/{id}', 'App\Controllers\CategoriaController', 'eliminar');

// Carrito
$router->add('GET', '/carrito', 'App\Controllers\CarritoController', 'index');
$router->add('POST', '/carrito/agregar', 'App\Controllers\CarritoController', 'agregar');
$router->add('POST', '/carrito/actualizar', 'App\Controllers\CarritoController', 'actualizar');
$router->add('POST', '/carrito/eliminar', 'App\Controllers\CarritoController', 'eliminar');
$router->add('POST', '/carrito/vaciar', 'App\Controllers\CarritoController', 'vaciar');
//sin esto no funcionaria bien:
$router->dispatch();


echo "<h1>Proyecto inicializado correctamente</h1>";