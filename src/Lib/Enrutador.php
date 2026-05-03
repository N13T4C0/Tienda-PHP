<?php
namespace Lib;


class Enrutador
{
    private static array $rutas = [];

   
      //Registra una ruta.
    
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
        $uri = strtok($uri, '?');                                              
        $uri = preg_replace('#^' . preg_quote(URL_BASE, '#') . '#', '', $uri);
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
