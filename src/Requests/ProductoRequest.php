<?php
namespace Requests;

class ProductoRequest
{
    // Valida los datos del formulario y devuelve un array con los errores encontrados
    public static function validar(array $datos): array
    {
        $errores = [];

        $nombre       = trim($datos['nombre'] ?? '');
        $precio       = $datos['precio'] ?? null;
        $stock        = $datos['stock'] ?? null;
        $categoriaId  = (int) ($datos['categoria_id'] ?? 0);

        if ($nombre === '') {
            $errores[] = 'El nombre del producto es obligatorio';
        }

        if ($precio === null || $precio === '' || (float) $precio < 0) {
            $errores[] = 'El precio debe ser un valor positivo';
        }

        if ($stock === null || $stock === '' || (int) $stock < 0) {
            $errores[] = 'El stock debe ser un valor positivo';
        }

        if ($categoriaId <= 0) {
            $errores[] = 'Debes seleccionar una categoria';
        }

        return $errores;
    }
}
