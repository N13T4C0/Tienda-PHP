<?php

namespace Requests;

class ProductoRequest
{
    /**
     * Sanea y valida los datos del formulario de producto (crear/editar).
     * Devuelve un array con 'errores' (array) y 'datos' (array saneado).
     */
    public static function validar(array $datos): array
    {
        $errores = [];

        // ── SANEAMIENTO ──────────────────────────────────────────────────────
        // htmlspecialchars: protege contra XSS convirtiendo caracteres especiales
        // (int) y (float): fuerza el tipo correcto y descarta cualquier texto extra
        $nombre       = htmlspecialchars(trim($datos['nombre']      ?? ''), ENT_QUOTES, 'UTF-8');
        $descripcion  = htmlspecialchars(trim($datos['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8');
        $categoriaId  = (int)   ($datos['categoria_id'] ?? 0);
        $precio       = (float) ($datos['precio']       ?? 0);
        $stock        = (int)   ($datos['stock']        ?? 0);
        $imagenActual = trim($datos['imagen_actual'] ?? 'sin-imagen.svg');
        $visible      = isset($datos['visible']) ? 1 : 0;

        // ── VALIDACION DE NOMBRE ──────────────────────────────────────────────
        if ($nombre === '') {
            $errores[] = 'El nombre del producto es obligatorio';
        } elseif (strlen($nombre) < 2) {
            $errores[] = 'El nombre debe tener al menos 2 caracteres';
        } elseif (strlen($nombre) > 200) {
            $errores[] = 'El nombre no puede superar 200 caracteres';
        }

        // ── VALIDACION DE DESCRIPCION ─────────────────────────────────────────
        if (strlen($descripcion) > 1000) {
            $errores[] = 'La descripcion no puede superar 1000 caracteres';
        }

        // ── VALIDACION DE PRECIO ──────────────────────────────────────────────
        if ($precio <= 0) {
            $errores[] = 'El precio debe ser un valor positivo';
        } elseif ($precio > 99999.99) {
            $errores[] = 'El precio no puede superar 99.999,99 euros';
        }

        // ── VALIDACION DE STOCK ───────────────────────────────────────────────
        if ($stock < 0) {
            $errores[] = 'El stock no puede ser negativo';
        } elseif ($stock > 99999) {
            $errores[] = 'El stock no puede superar 99.999 unidades';
        }

        // ── VALIDACION DE CATEGORIA ───────────────────────────────────────────
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
