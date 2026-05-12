<?php

namespace Requests;

class CategoriaRequest
{
    public static function validar(array $datos): array
    {
        $errores = [];

        //SANITIZAR TODOS LOS DATOS
        $nombre      = htmlspecialchars(trim($datos['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
        $descripcion = htmlspecialchars(trim($datos['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8');

        //VALIDAR
        if ($nombre === '') {
            $errores[] = 'El nombre de la categoría es obligatorio';
        }

        if (strlen($nombre) < 3) {
            $errores[] = 'El nombre debe tener al menos 3 caracteres';
        }

        if (strlen($nombre) > 100) {
            $errores[] = 'El nombre no puede exceder 100 caracteres';
        }

        if (strlen($descripcion) > 500) {
            $errores[] = 'La descripción no puede exceder 500 caracteres';
        }

        // DEVOLVER ERRORES Y DATOS EN UN ARRAY ASOCIATIVO
        return[
            'errores' => $errores,
            'datos' => [
                'nombre' => $nombre,
                'descripcion' =>$descripcion
            ]
        ];
    }
}
