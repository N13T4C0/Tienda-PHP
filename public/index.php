<?php
/**
 * Punto de entrada unico de la aplicacion (Front Controller + Bootstrap).
 *
 * Este archivo hace tres cosas en orden:
 *   1. Define las constantes de ruta y la URL base
 *   2. Prepara el entorno: carga .env, registra el autoloader, arranca sesion
 *   3. Registra las rutas y despacha la peticion actual
 */

// -------------------------------------------------------
// 1. CONSTANTES DE RUTA
// -------------------------------------------------------
define('RAIZ',    dirname(__DIR__));        // C:/xampp/htdocs/ProyectoTiendaPHP
define('APP',     RAIZ . '/src');           // .../src
define('CONFIG',  RAIZ . '/config');        // .../config
define('PUBLICO', RAIZ . '/public');        // .../public

// Cambia este valor si renombras la carpeta del proyecto
define('URL_BASE', '/ProyectoTiendaPHP');

// -------------------------------------------------------
// 2. BOOTSTRAP
// -------------------------------------------------------

// Cargamos el lector de .env antes del autoloader (aun no esta activo)
require_once APP . '/Utils/Utilidades.php';
\Utils\Utilidades::cargar(RAIZ . '/.env');

// Autoloader de Composer (PHPMailer y otras librerias externas)
$composerAutoload = RAIZ . '/vendor/autoload.php';
if (is_file($composerAutoload)) {
    require_once $composerAutoload;
}

// Autoloader manual: convierte namespace en ruta de archivo
// Ejemplo: "Servicios\UsuarioServicio" -> src/Servicios/UsuarioServicio.php
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

// Iniciamos sesion si no esta iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -------------------------------------------------------
// 3. RUTAS
// -------------------------------------------------------
\Rutas\Rutas::registrar();
