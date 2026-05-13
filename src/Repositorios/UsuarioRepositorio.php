<?php

namespace Repositorios;

use Lib\Conexion;
use Modelos\Usuario;
use PDO;

class UsuarioRepositorio
{
    private $bd;

    public function __construct()
    {
        $this->bd = Conexion::abrir();
    }

    private function hidratar(array $filas): array
    {
        return array_map(fn($fila) => Usuario::fromArray($fila), $filas);
    }

    public function encontrarPorEmail(string $email): ?Usuario
    {
        $stmt = $this->bd->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $fila = $stmt->fetch();
        return $fila ? Usuario::fromArray($fila) : null;
    }

    public function encontrarPorId(int $id): ?Usuario
    {
        $stmt = $this->bd->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ? Usuario::fromArray($fila) : null;
    }

    public function encontrarPorToken(string $token): ?Usuario
    {
        // Validamos que el token no tenga más de 1 minuto
        $sql = "SELECT * FROM usuarios 
                WHERE token_email = :tok 
                AND activado = 0 
                AND token_email_creado >= (NOW() - INTERVAL 1 MINUTE)";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':tok' => $token]);
        $fila = $stmt->fetch();
        return $fila ? Usuario::fromArray($fila) : null;
    }

    public function insertar(array $datos): int
    {
        $sql = "INSERT INTO usuarios (nombre, apellidos, email, clave, rol, activado, token_email, token_email_creado)
                VALUES (:nom, :ape, :email, :clave, :rol, :act, :tok, NOW())";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':nom'   => $datos['nombre'],
            ':ape'   => $datos['apellidos'] ?? '',
            ':email' => $datos['email'],
            ':clave' => $datos['clave'],
            ':rol'   => $datos['rol'] ?? 'cliente',
            ':act'   => $datos['activado'] ?? 0,
            ':tok'   => $datos['token_email'] ?? null
        ]);
        return (int) $this->bd->lastInsertId();
    }

    public function activar(int $id): bool
    {
        $stmt = $this->bd->prepare("UPDATE usuarios SET activado = 1, token_email = NULL, token_email_creado = NULL WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT * FROM usuarios ORDER BY fecha_alta DESC";
        return $this->hidratar($this->bd->query($sql)->fetchAll());
    }

    public function contarTodos(): int
    {
        return (int) $this->bd->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->bd->prepare("DELETE FROM usuarios WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function guardarDesdeGoogle(array $datos): int
    {
        // Buscar si ya existe por Google ID o Email
        $stmt = $this->bd->prepare("SELECT id FROM usuarios WHERE google_id = :gid OR email = :email LIMIT 1");
        $stmt->execute([':gid' => $datos['google_id'], ':email' => $datos['email']]);
        $existente = $stmt->fetch();

        if ($existente) {
            $stmt = $this->bd->prepare("UPDATE usuarios SET nombre = :nom, apellidos = :ape, avatar = :av WHERE id = :id");
            $stmt->execute([
                ':nom' => $datos['nombre'],
                ':ape' => $datos['apellidos'],
                ':av'  => $datos['avatar'],
                ':id'  => $existente['id']
            ]);
            return (int) $existente['id'];
        }

        $sql = "INSERT INTO usuarios (google_id, email, nombre, apellidos, avatar, rol, activado)
                VALUES (:gid, :email, :nom, :ape, :av, 'cliente', 1)";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':gid'   => $datos['google_id'],
            ':email' => $datos['email'],
            ':nom'   => $datos['nombre'],
            ':ape'   => $datos['apellidos'],
            ':av'    => $datos['avatar']
        ]);
        return (int) $this->bd->lastInsertId();
    }
}
