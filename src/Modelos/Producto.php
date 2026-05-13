<?php

namespace Modelos;

class Producto
{
    public function __construct(
        public ?int $id = null,
        public int $categoria_id = 0,
        public string $nombre = '',
        public string $descripcion = '',
        public float $precio = 0.0,
        public int $stock = 0,
        public string $imagen = 'sin-imagen.svg',
        public bool $visible = true,
        public ?string $categoria_nombre = null
    ) {}

    /**
     * Mapea un array asociativo de la DB a un objeto Producto
     */
    public static function fromArray(array $datos): self
    {
        return new self(
            id: isset($datos['id']) ? (int)$datos['id'] : null,
            categoria_id: (int)($datos['categoria_id'] ?? 0),
            nombre: $datos['nombre'] ?? '',
            descripcion: $datos['descripcion'] ?? '',
            precio: (float)($datos['precio'] ?? 0.0),
            stock: (int)($datos['stock'] ?? 0),
            imagen: $datos['imagen'] ?? 'sin-imagen.svg',
            visible: isset($datos['visible']) ? (bool)$datos['visible'] : true,
            categoria_nombre: $datos['categoria_nombre'] ?? null
        );
    }
}
