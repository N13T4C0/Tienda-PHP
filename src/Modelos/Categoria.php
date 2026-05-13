<?php

namespace Modelos;

class Categoria
{
    public function __construct(
        public ?int $id = null,
        public string $nombre = '',
        public string $descripcion = ''
    ) {}

    /**
     * Convierte el array de la BD en un objeto Categoria
     */
    public static function fromArray(array $datos): self
    {
        return new self(
            id: isset($datos['id']) ? (int)$datos['id'] : null,
            nombre: $datos['nombre'] ?? '',
            descripcion: $datos['descripcion'] ?? ''
        );
    }
}
