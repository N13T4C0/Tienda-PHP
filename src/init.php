<?php
// Rutas absolutas del proyecto
define('RAIZ',    dirname(__DIR__));
define('APP',     RAIZ . '/src');
define('CONFIG',  RAIZ . '/config');
define('PUBLICO', RAIZ . '/public');
define('URL_BASE', '/ProyectoTiendaPHP'); 

// Carga .env antes de que el autoloader este activo
require_once APP . '/Utils/Utilidades.php';
\Utils\Utilidades::cargar(RAIZ . '/.env');

// Composer (PHPMailer y librerias externas)
if (is_file(RAIZ . '/vendor/autoload.php')) {
    require_once RAIZ . '/vendor/autoload.php';
}


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

\Rutas\Rutas::registrar();