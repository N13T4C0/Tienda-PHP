<?php
namespace Config;

use PDO;
use PDOException;

/**
 * Conexion PDO unica a la base de datos.
 * Patron: clase con metodo estatico que devuelve el PDO.
 */

class Conexion
{
    private static $pdo = null;

    /** Datos de conexion */
    private const HOST    = 'localhost';
    private const BD      = 'tiendaphp';
    private const USUARIO = 'root';
    private const CLAVE   = '';
    private const CHARSET = 'utf8mb4';

    /**
     * Devuelve la instancia PDO. Si no existe, la crea.
     */
    public static function abrir(): PDO
    {
        if (self::$pdo === null) {
            $dsn = 'mysql:host=' . self::HOST
                 . ';dbname='   . self::BD
                 . ';charset='  . self::CHARSET;

            // ERRMODE_EXCEPTION: los errores SQL lanzan excepciones en lugar de fallar en silencio
            // FETCH_ASSOC: los resultados vienen como array con nombres de columna ($fila['nombre'])
            // EMULATE_PREPARES false: usa prepared statements reales, SQL injection mas seguro
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$pdo = new PDO($dsn, self::USUARIO, self::CLAVE, $opciones);
            } catch (PDOException $e) {
                die('No se ha podido conectar con la base de datos: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
