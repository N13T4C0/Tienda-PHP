<?php
namespace Lib;

/**
 * Enrutador - Gestiona el registro y despacho de rutas HTTP.
 *
 * Uso basico:
 *   Enrutador::agregar('GET', '/ruta/', function() { ... });
 *   Enrutador::despachar();
 *
 * Soporta rutas con parametro dinamico al final:
 *   Enrutador::agregar('GET', '/producto/detalle/:id', function($id) { ... });
 */
class Enrutador
{
    /** @var array<string, array<string, callable>> */
    private static array $rutas = [];

    /**
     * Registra una ruta.
     *
     * @param string   $metodo     Metodo HTTP: 'GET' o 'POST'
     * @param string   $accion     Patron de ruta, ej: '/auth/login/' o '/producto/detalle/:id'
     * @param callable $controlador Funcion anonima que ejecuta la accion
     */
    public static function agregar(string $metodo, string $accion, callable $controlador): void
    {
        $accion = trim($accion, '/');
        self::$rutas[$metodo][$accion] = $controlador;
    }

    /**
     * Despacha la peticion actual a la ruta correspondiente.
     * Muestra 404 si no encuentra coincidencia.
     */
    public static function despachar(): void
    {
        $metodo = $_SERVER['REQUEST_METHOD'];

        // Limpiamos la URI: quitamos query string y la base del proyecto
        $uri = $_SERVER['REQUEST_URI'];
        $uri = strtok($uri, '?');                                              // sin query string
        $uri = preg_replace('#^' . preg_quote(URL_BASE, '#') . '#', '', $uri); // sin prefijo base
        $uri = trim($uri, '/');

        // 1. Intentamos coincidencia exacta
        $fn    = self::$rutas[$metodo][$uri] ?? null;
        $param = null;

        // 2. Si no hay coincidencia exacta, extraemos el ultimo segmento como :id
        if (!$fn && preg_match('#^(.+)/([^/]+)$#', $uri, $m)) {
            $uriBase = $m[1] . '/:id';
            $param   = $m[2];
            $fn      = self::$rutas[$metodo][$uriBase] ?? null;
        }

        if ($fn) {
            call_user_func($fn, $param);
        } else {
            http_response_code(404);
            require APP . '/Vistas/errores/404.php';
        }
    }
}
