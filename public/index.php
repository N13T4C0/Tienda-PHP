<?php
/**
 * Front Controller (punto de entrada unico).
 *
 * 1. Define constantes de ruta.
 * 2. Incluye el bootstrap (src/init.php): autoload + sesion.
 * 3. Delega el enrutamiento a Rutas::registrar().
 */

// Constantes de ruta
define('RAIZ',    dirname(__DIR__));
define('APP',     RAIZ . '/src');
define('CONFIG',  RAIZ . '/config');
define('PUBLICO', RAIZ . '/public');

// URL base del proyecto (cambia si renombras la carpeta)
define('URL_BASE', '/ProyectoTiendaPHP');

// Bootstrap: autoload + sesion
require_once APP . '/init.php';

// Cargamos el archivo de rutas y despachamos
require_once APP . '/Rutas/Rutas.php';
Rutas::registrar();

require APP . '/Lib/GoogleOAuth.php';
