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

// Autoloader manual: namespace -> ruta de archivo
spl_autoload_register(function (string $clase) {
    $mapa = [
        'Config\\'        => CONFIG . '/',
        'Modelos\\'       => APP . '/Modelos/',
        'Controladores\\' => APP . '/Controladores/',
        'Lib\\'           => APP . '/Lib/',
        'Rutas\\'         => APP . '/Rutas/',
        'Utils\\'         => APP . '/Utils/',
        'Middleware\\'    => APP . '/Middleware/',
        'Requests\\'      => APP . '/Requests/',
        'Repositorios\\'  => APP . '/Repositorios/',
        'Servicios\\'     => APP . '/Servicios/',
    ];

    foreach ($mapa as $prefijo => $directorio) {
        if (str_starts_with($clase, $prefijo)) {
            $archivo = $directorio . str_replace('\\', '/', substr($clase, strlen($prefijo))) . '.php';
            if (is_file($archivo)) {
                require_once $archivo;
                return;
            }
        }
    }
});

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

\Rutas\Rutas::registrar();