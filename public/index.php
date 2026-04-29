<?php
/**
 * Front Controller (punto de entrada unico).
 *
 * - Inicia la sesion.
 * - Carga las clases necesarias (autoload manual).
 * - Lee la ruta de la URL y despacha al controlador adecuado.
 */

session_start();

// Definimos rutas base
define('RAIZ',    dirname(__DIR__));
define('APP',     RAIZ . '/app');
define('PUBLICO', RAIZ . '/public');

// URL base del proyecto (cambia si renombras la carpeta del proyecto).
// Gracias al .htaccess de la raiz no hace falta /public en la URL.
define('URL_BASE', '/ProyectoTiendaPHP');

// -----------------------------------------------------
//  Autoload muy sencillo
// -----------------------------------------------------
spl_autoload_register(function (string $clase) {
    $rutasPosibles = [
        APP . '/config/'      . $clase . '.php',
        APP . '/modelos/'     . $clase . '.php',
        APP . '/controladores/'. $clase . '.php',
        APP . '/ayudantes/'   . $clase . '.php',
    ];
    foreach ($rutasPosibles as $ruta) {
        if (is_file($ruta)) {
            require_once $ruta;
            return;
        }
    }
});

// -----------------------------------------------------
//  Mini router por convencion: ruta = controlador/accion/parametros
// -----------------------------------------------------
$rutaCruda = $_GET['ruta'] ?? '';
$partes    = $rutaCruda === '' ? [] : explode('/', trim($rutaCruda, '/'));

$nombreControlador = !empty($partes[0]) ? ucfirst(strtolower($partes[0])) . 'Controlador' : 'HomeControlador';
$nombreAccion      = $partes[1] ?? 'index';
$parametros        = array_slice($partes, 2);

// Si la clase no existe, mostramos un 404 sencillo
if (!class_exists($nombreControlador)) {
    http_response_code(404);
    require APP . '/vistas/errores/404.php';
    exit;
}

$controlador = new $nombreControlador();

// Si el metodo no existe, mostramos 404
if (!method_exists($controlador, $nombreAccion)) {
    http_response_code(404);
    require APP . '/vistas/errores/404.php';
    exit;
}

// Llamada a la accion con los parametros que vinieran en la URL
call_user_func_array([$controlador, $nombreAccion], $parametros);
