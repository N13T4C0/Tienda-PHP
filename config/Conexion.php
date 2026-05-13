<?php

namespace Config;

use PDO;
use PDOException;

/** Conexion PDO unica a la base de datos (Singleton). Lee credenciales del .env */
class Conexion
{
    private static ?PDO $pdo = null;

    public static function abrir(): PDO
    {
        if (self::$pdo === null) {
            $host    = $_ENV['DB_HOST']    ?? 'localhost';
            $bd      = $_ENV['DB_NAME']    ?? 'tiendaphp';
            $usuario = $_ENV['DB_USER']    ?? 'root';
            $clave   = $_ENV['DB_PASS']    ?? '';
            $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

            $dsn = "mysql:host={$host};dbname={$bd};charset={$charset}";

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
