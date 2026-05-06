<?php
namespace Utils;


class Utilidades
{
    /** Carga el archivo .env indicado. Devuelve true si lo encontro. */
    public static function cargar(string $rutaArchivo): bool
    {
        // Verifica que el archivo .env existe
        if (!is_file($rutaArchivo)) {
            return false;
        }

        // Lee todas las líneas del archivo (ignora saltos de línea y vacías)
        $lineas = file($rutaArchivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lineas as $linea) {
            $linea = trim($linea);

            // Salta comentarios (líneas con #) y líneas sin '='
            if ($linea === '' || $linea[0] === '#' || !str_contains($linea, '=')) {
                continue;
            }

            // Divide por '=' y extrae clave y valor (ej: DB_HOST=localhost)
            [$clave, $valor] = array_map('trim', explode('=', $linea, 2));

            // Quita comillas del valor (ej: "localhost" → localhost)
            $valor = trim($valor, "\"'");

            // Guarda en $_ENV para acceso en PHP
            $_ENV[$clave] = $valor;
            // Guarda también en el sistema operativo
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
