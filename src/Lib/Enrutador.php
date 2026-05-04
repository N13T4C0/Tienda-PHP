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

    // Punto de entrada principal. Lee la peticion actual, busca la ruta
    // que le corresponde y ejecuta su controlador. Si no la encuentra muestra el 404.
    public static function despachar(): void
    {
        $metodo = $_SERVER['REQUEST_METHOD']; // GET, POST, etc.
        $uri    = self::limpiarUri($_SERVER['REQUEST_URI']);

        // Devuelve el controlador y el parametro :id si lo hay
        [$fn, $param] = self::resolverRuta($metodo, $uri);

        if ($fn) {
            call_user_func($fn, $param); // ejecuta el controlador pasandole el id
        } else {
            http_response_code(404);
            require APP . '/Vistas/errores/404.php';
        }
    }

    // Deja la URI limpia para compararla con las rutas registradas.
    private static function limpiarUri(string $uri): string
    {
        // strtok corta la URI por el '?' y devuelve solo la parte izquierda
        // ej: /ProyectoTiendaPHP/productos?q=camiseta  →  /ProyectoTiendaPHP/productos
        $uri = strtok($uri, '?');

        // preg_quote escapa los caracteres especiales de URL_BASE para usarlos en una regex
        // preg_replace elimina el prefijo del proyecto al inicio de la URI
        // ej: /ProyectoTiendaPHP/productos  →  /productos
        $uri = preg_replace('#^' . preg_quote(URL_BASE, '#') . '#', '', $uri);

        // Elimina las barras del inicio y del final para comparar limpio
        // ej: /productos/  →  productos
        return trim($uri, '/');
    }

    private static function resolverRuta(string $metodo, string $uri): array
    {
        // Coincidencia exacta: "productos" → busca $rutas['GET']['productos']
        if ($fn = self::$rutas[$metodo][$uri] ?? null) {
            return [$fn, null];
        }

        // Parte la uri por las barras y saca el último trozo como id
        // ej: "productos/5"  →  $partes=["productos"]  $id="5"
        $partes = explode('/', $uri);
        $id     = array_pop($partes);    // extrae el último segmento
        $base   = implode('/', $partes); // vuelve a unir el resto

        // Busca la ruta con el comodín :id
        // ej: busca $rutas['GET']['productos/:id']
        $fn = self::$rutas[$metodo][$base . '/:id'] ?? null;
        return [$fn, $id];
    }
}
