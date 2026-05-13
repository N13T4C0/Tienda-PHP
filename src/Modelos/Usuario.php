<?php

namespace Modelos;

class Usuario
{
    public function __construct(
        public ?int $id = null,
        public ?string $google_id = null,
        public string $nombre = '',
        public string $apellidos = '',
        public string $email = '',
        public ?string $clave = null,
        public string $rol = 'cliente',
        public bool $activado = false,
        public ?string $avatar = null,
        public ?string $token_email = null,
        public ?string $token_email_creado = null,
        public ?string $fecha_alta = null
    ) {}

    public static function fromArray(array $datos): self
    {
        return new self(
            id: isset($datos['id']) ? (int)$datos['id'] : null,
            google_id: $datos['google_id'] ?? null,
            nombre: $datos['nombre'] ?? '',
            apellidos: $datos['apellidos'] ?? '',
            email: $datos['email'] ?? '',
            clave: $datos['clave'] ?? null,
            rol: $datos['rol'] ?? 'cliente',
            activado: (bool)($datos['activado'] ?? false),
            avatar: $datos['avatar'] ?? null,
            token_email: $datos['token_email'] ?? null,
            token_email_creado: $datos['token_email_creado'] ?? null,
            fecha_alta: $datos['fecha_alta'] ?? null
        );
    }

    /**
     * Helper para saber si el usuario es administrador
     */
    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }
}
