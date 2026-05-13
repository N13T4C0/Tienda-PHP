<?php
define('RAIZ',    dirname(__DIR__));
define('APP',     RAIZ . '/src');
define('CONFIG',  RAIZ . '/config');
define('PUBLICO', RAIZ . '/public');
define('URL_BASE', '/ProyectoTiendaPHP');

require_once RAIZ . '/vendor/autoload.php';

// Luego .env
require_once APP . '/Utils/Utilidades.php';
\Utils\Utilidades::cargar(RAIZ . '/.env');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

\Rutas\Rutas::registrar();
