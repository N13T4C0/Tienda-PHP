<?php

namespace Config;

use PDO;
use PDOException;

/**
 * Conexion PDO unica a la base de datos (patron Singleton).
 *
 * Las credenciales ya NO estan escritas aqui directamente.
 * Se leen desde el archivo .env cargado al inicio en init.php.
 *
 * Ventajas de usar .env:
 *  - En local pones tus credenciales sin afectar al resto del equipo
 *  - En produccion pones las credenciales reales sin tocar el codigo
 *  - No se sube informacion sensible a Git (añade .env al .gitignore)
 */
class Conexion
{
    private static ?PDO $pdo = null;

    /**
     * Devuelve la instancia PDO compartida.
     * Si todavia no existe, la crea leyendo los valores del .env.
     */
    public static function abrir(): PDO
    {
        if (self::$pdo === null) {
            // Leemos las credenciales desde las variables de entorno
            // cargadas en init.php con Utilidades::cargar('.env')
            $host    = $_ENV['DB_HOST']    ?? 'localhost';
            $bd      = $_ENV['DB_NAME']    ?? 'tiendaphp';
            $usuario = $_ENV['DB_USER']    ?? 'root';
            $clave   = $_ENV['DB_PASS']    ?? '';
            $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

            $dsn = "mysql:host={$host};dbname={$bd};charset={$charset}";

            // ERRMODE_EXCEPTION: los errores SQL lanzan excepciones
            // FETCH_ASSOC:       resultados como array ['columna' => valor]
            // EMULATE_PREPARES:  false → prepared statements reales (mas seguro)
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$pdo = new PDO($dsn, $usuario, $clave, $opciones);
            } catch (PDOException $e) {
                die('No se ha podido conectar con la base de datos: ' . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
