<?php

namespace Requests;

class ProductoRequest
{
    // Valida los datos del formulario y devuelve un array con los errores y los datos limpios
    public static function validar(array $datos): array
    {
        $errores = [];

        //SANEAR
        $nombre       = htmlspecialchars(trim($datos['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
        $descripcion  = htmlspecialchars(trim($datos['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8');
        $categoriaId  = (int) ($datos['categoria_id'] ?? 0);
        $precio       = (float) ($datos['precio'] ?? 0);
        $stock        = (int) ($datos['stock'] ?? 0);
        $imagenActual = trim($datos['imagen_actual'] ?? 'sin-imagen.svg');
        $visible      = isset($datos['visible']) ? 1 : 0;

        // VALIDAR
        if ($nombre === '') {
            $errores[] = 'El nombre del producto es obligatorio';
        }

        if ($precio <= 0) {
            $errores[] = 'El precio debe ser un valor positivo';
        }

        if ($stock < 0) {
            $errores[] = 'El stock no puede ser un valor negativo';
        }

        if ($categoriaId <= 0) {
            $errores[] = 'Debes seleccionar una categoria';
        }

        return [
            'errores' => $errores,
            'datos' => [
                'categoria_id' => $categoriaId,
                'nombre'       => $nombre,
                'descripcion'  => $descripcion,
                'precio'       => $precio,
                'stock'        => $stock,
                'imagen'       => $imagenActual,
                'visible'      => $visible
            ]
        ];
    }
}
