<?php

namespace Requests;

class ProductoRequest
{
    public static function validar(array $datos): array
    {
        $errores = [];

        $nombre       = htmlspecialchars(trim($datos['nombre']      ?? ''), ENT_QUOTES, 'UTF-8');
        $descripcion  = htmlspecialchars(trim($datos['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8');
        $categoriaId  = (int)   ($datos['categoria_id'] ?? 0);
        $precio       = (float) ($datos['precio']       ?? 0);
        $stock        = (int)   ($datos['stock']        ?? 0);
        $imagenActual = trim($datos['imagen_actual'] ?? 'sin-imagen.svg');
        $visible      = isset($datos['visible']) ? 1 : 0;

        if ($nombre === '') {
            $errores[] = 'El nombre del producto es obligatorio';
        } elseif (strlen($nombre) > 200) {
            $errores[] = 'El nombre no puede superar 200 caracteres';
        }

        if (strlen($descripcion) > 1000) {
            $errores[] = 'La descripcion no puede superar 1000 caracteres';
        }

        if ($precio <= 0) {
            $errores[] = 'El precio debe ser un valor positivo';
        }

        if ($stock < 0) {
            $errores[] = 'El stock no puede ser negativo';
        }

        if ($categoriaId <= 0) {
            $errores[] = 'Debes seleccionar una categoria';
        }

        return [
            'errores' => $errores,
            'datos'   => [
                'categoria_id' => $categoriaId,
                'nombre'       => $nombre,
                'descripcion'  => $descripcion,
                'precio'       => $precio,
                'stock'        => $stock,
                'imagen'       => $imagenActual,
                'visible'      => $visible,
            ],
        ];
    }
}
