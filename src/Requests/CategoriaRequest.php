<?php

namespace Requests;

class CategoriaRequest
{
    public static function validar(array $datos): array
    {
        $errores = [];

        $nombre      = htmlspecialchars(trim($datos['nombre']      ?? ''), ENT_QUOTES, 'UTF-8');
        $descripcion = htmlspecialchars(trim($datos['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8');

        if ($nombre === '') {
            $errores[] = 'El nombre de la categoria es obligatorio';
        } elseif (strlen($nombre) < 3) {
            $errores[] = 'El nombre debe tener al menos 3 caracteres';
        } elseif (strlen($nombre) > 100) {
            $errores[] = 'El nombre no puede superar 100 caracteres';
        }

        if (strlen($descripcion) > 500) {
            $errores[] = 'La descripcion no puede superar 500 caracteres';
        }

        return [
            'errores' => $errores,
            'datos'   => ['nombre' => $nombre, 'descripcion' => $descripcion],
        ];
    }
}
