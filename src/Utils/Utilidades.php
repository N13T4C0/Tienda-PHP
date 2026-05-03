<?php
namespace Utils;

/**
 * Mini lector de archivos .env (formato CLAVE=valor).
 *
 * Carga las variables del .env en $_ENV y getenv()
 * sin depender de ninguna libreria externa.
 */
class Utilidades
{
    /** Carga el archivo .env indicado. Devuelve true si lo encontro. */
    public static function cargar(string $rutaArchivo): bool
    {
        if (!is_file($rutaArchivo)) {
            return false;
        }
        $lineas = file($rutaArchivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lineas as $linea) {
            $linea = trim($linea);
            // Saltamos comentarios y lineas sin '='
            if ($linea === '' || $linea[0] === '#' || !str_contains($linea, '=')) {
                continue;
            }
            [$clave, $valor] = array_map('trim', explode('=', $linea, 2));
            // Quitamos comillas alrededor del valor si las hubiera
            $valor = trim($valor, "\"'");
            $_ENV[$clave] = $valor;
            putenv($clave . '=' . $valor);
        }
        return true;
    }

    /** Devuelve el valor de una clave, con valor por defecto si no existe */
    public static function obtener(string $clave, $porDefecto = null)
    {
        return $_ENV[$clave] ?? getenv($clave) ?: $porDefecto;
    }
}
