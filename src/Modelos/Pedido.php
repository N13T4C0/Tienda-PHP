<?php

namespace Modelos;

class Pedido
{
    public function __construct(
        public ?int $id = null,
        public int $usuario_id = 0,
        public float $importe_total = 0.0,
        public string $direccion = '',
        public string $localidad = '',
        public string $provincia = '',
        public ?string $fecha_pedido = null,
        public string $estado = 'pendiente'
    ) {}

    public static function fromArray(array $datos): self
    {
        return new self(
            id: isset($datos['id']) ? (int)$datos['id'] : null,
            usuario_id: (int)($datos['usuario_id'] ?? 0),
            importe_total: (float)($datos['importe_total'] ?? 0.0),
            direccion: $datos['direccion'] ?? '',
            localidad: $datos['localidad'] ?? '',
            provincia: $datos['provincia'] ?? '',
            fecha_pedido: $datos['fecha_pedido'] ?? null,
            estado: $datos['estado'] ?? 'pendiente'
        );
    }
}
