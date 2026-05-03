<?php

/**
 * Inicializacion de la aplicacion (bootstrap).
 *
 * - Carga las variables del archivo .env
 * - Registra el autoloader manual de clases propias
 * - Incluye el autoloader de Composer (para PHPMailer y otras librerias)
 * - Arranca la sesion
 */

// Constantes de ruta
if (!defined('RAIZ'))    define('RAIZ',    dirname(__DIR__));
if (!defined('APP'))     define('APP',     RAIZ . '/src');
if (!defined('CONFIG'))  define('CONFIG',  RAIZ . '/config');
if (!defined('PUBLICO')) define('PUBLICO', RAIZ . '/public');

// Cargamos el lector de .env (sin depender del autoloader todavia)
require_once APP . '/Utils/Utilidades.php';
Utilidades::cargar(RAIZ . '/.env');

// Autoloader de Composer (PHPMailer y otras librerias externas)
$composerAutoload = RAIZ . '/vendor/autoload.php';
if (is_file($composerAutoload)) {
    require_once $composerAutoload;
}

// Autoload manual de clases propias del proyecto
spl_autoload_register(function (string $clase) {
    $rutasPosibles = [
        CONFIG              . '/' . $clase . '.php',
        APP . '/Modelos/'       . $clase . '.php',
        APP . '/Controladores/' . $clase . '.php',
        APP . '/Lib/'           . $clase . '.php',
        APP . '/Rutas/'         . $clase . '.php',
        APP . '/Utils/'         . $clase . '.php',
    ];
    foreach ($rutasPosibles as $ruta) {
        if (is_file($ruta)) {
            require_once $ruta;
            return;
        }
    }
});

// Iniciamos sesion si no esta iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
