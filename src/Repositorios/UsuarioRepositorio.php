<?php
namespace Repositorios;

use Config\Conexion;


class UsuarioRepositorio
{
    private $bd;

    public function __construct()
    {
        $this->bd = Conexion::abrir();
    }

    /** Inserta un usuario nuevo y devuelve su id */
    public function insertar(array $datos): int
    {
        $sql = "INSERT INTO usuarios
                    (nombre, apellidos, email, clave, rol, activado, token_email)
                VALUES
                    (:nom, :ape, :email, :clave, :rol, :act, :tok)";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':nom'   => $datos['nombre'],
            ':ape'   => $datos['apellidos'] ?? '',
            ':email' => $datos['email'],
            ':clave' => $datos['clave'],
            ':rol'   => $datos['rol'] ?? 'cliente',
            ':act'   => $datos['activado'] ?? 0,
            ':tok'   => $datos['token_email'] ?? null,
        ]);
        return (int) $this->bd->lastInsertId();
    }

    /** Busca un usuario por su email */
    public function encontrarPorEmail(string $email): ?array
    {
        $stmt = $this->bd->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    /** Busca un usuario por su id */
    public function encontrarPorId(int $id): ?array
    {
        $stmt = $this->bd->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    /** Busca un usuario por su token de activacion de email */
    public function encontrarPorToken(string $token): ?array
    {
        $stmt = $this->bd->prepare(
            "SELECT * FROM usuarios WHERE token_email = :tok AND activado = 0"
        );
        $stmt->execute([':tok' => $token]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    /** Activa la cuenta del usuario */
    public function activar(int $id): bool
    {
        $stmt = $this->bd->prepare(
            "UPDATE usuarios SET activado = 1, token_email = NULL WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    /** Devuelve todos los usuarios (para el panel admin) */
    public function obtenerTodos(): array
    {
        return $this->bd
            ->query("SELECT id, nombre, apellidos, email, rol, activado, fecha_alta
                     FROM usuarios ORDER BY fecha_alta DESC")
            ->fetchAll();
    }

    /** Elimina un usuario por su id */
    public function eliminar(int $id): bool
    {
        $stmt = $this->bd->prepare("DELETE FROM usuarios WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Si ya existe un usuario con ese google_id o email lo actualiza;
     * si no, lo inserta. Devuelve el id del usuario.
     */
    public function guardarDesdeGoogle(array $datos): int
    {
        $stmt = $this->bd->prepare(
            "SELECT id FROM usuarios WHERE google_id = :gid OR email = :email LIMIT 1"
        );
        $stmt->execute([
            ':gid'   => $datos['google_id'],
            ':email' => $datos['email'],
        ]);
        $existente = $stmt->fetch();

        if ($existente) {
            $stmt = $this->bd->prepare(
                "UPDATE usuarios SET nombre = :nom, apellidos = :ape, avatar = :av
                 WHERE id = :id"
            );
            $stmt->execute([
                ':nom' => $datos['nombre'],
                ':ape' => $datos['apellidos'],
                ':av'  => $datos['avatar'],
                ':id'  => $existente['id'],
            ]);
            return (int) $existente['id'];
        }

        $stmt = $this->bd->prepare(
            "INSERT INTO usuarios (google_id, email, nombre, apellidos, avatar, rol, activado)
             VALUES (:gid, :email, :nom, :ape, :av, 'cliente', 1)"
        );
        $stmt->execute([
            ':gid'   => $datos['google_id'],
            ':email' => $datos['email'],
            ':nom'   => $datos['nombre'],
            ':ape'   => $datos['apellidos'],
            ':av'    => $datos['avatar'],
        ]);
        return (int) $this->bd->lastInsertId();
    }
}
