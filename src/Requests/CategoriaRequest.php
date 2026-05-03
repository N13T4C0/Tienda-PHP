<?php
namespace Requests;

class CategoriaRequest
{
    // Valida los datos del formulario y devuelve un array con los errores encontrados
    public static function validar(array $datos): array
    {
        $errores = [];

        $nombre = trim($datos['nombre'] ?? '');

        if ($nombre === '') {
            $errores[] = 'El nombre de la categoria es obligatorio';
        }

        return $errores;
    }
}
