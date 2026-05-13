<?php
namespace Modelos;

use Config\Conexion;


class Usuario
{
    private $bd;

    public function __construct()
    {
        $this->bd = Conexion::abrir();
    }

    /** Crea un usuario nuevo */
    public function registrar(array $datos): int
    {
        $sql = "INSERT INTO usuarios
                    (nombre, apellidos, email, clave, rol, activado, token_email, token_email_creado)
                VALUES
                    (:nom, :ape, :email, :clave, :rol, :act, :tok, :tok_creado)";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':nom'   => $datos['nombre'],
            ':ape'   => $datos['apellidos'] ?? '',
            ':email' => $datos['email'],
            ':clave' => $datos['clave'],
            ':rol'   => $datos['rol'] ?? 'cliente',
            ':act'   => $datos['activado'] ?? 0,
            ':tok'   => $datos['token_email'] ?? null,
            ':tok_creado' => $datos['token_email_creado'] ?? null,
        ]);
        return (int) $this->bd->lastInsertId();
    }

    /** Busca un usuario por su email */
    public function buscarPorEmail(string $email): ?array
    {
        $stmt = $this->bd->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    /** Busca un usuario por su id */
    public function obtenerUno(int $id): ?array
    {
        $stmt = $this->bd->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    /** Busca un usuario por su token de activacion de email */
    public function buscarPorToken(string $token): ?array
    {
        $stmt = $this->bd->prepare(
            "SELECT * FROM usuarios
             WHERE token_email = :tok
               AND activado = 0
               AND token_email_creado IS NOT NULL
               AND token_email_creado >= (NOW() - INTERVAL 1 MINUTE)"
        );
        $stmt->execute([':tok' => $token]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    /** Activa la cuenta del usuario (tras hacer click en el email) */
    public function activarCuenta(int $id): bool
    {
        $stmt = $this->bd->prepare(
            "UPDATE usuarios SET activado = 1, token_email = NULL, token_email_creado = NULL WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    /** Lista todos los usuarios (para el admin) */
    public function listar(): array
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

public function registrarOActualizarGoogle(array $datos): int
{
    // 1. Usamos $this->bd que es como se llama la conexión en esta clase
    $sql = "SELECT id FROM usuarios WHERE google_id = :google_id OR email = :email LIMIT 1";
    $stmt = $this->bd->prepare($sql);
    $stmt->execute([
        ':google_id' => $datos['google_id'],
        ':email'     => $datos['email']
    ]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        // 2. Actualizar si ya existe
        $sql = "UPDATE usuarios SET 
                nombre = :nombre, 
                apellidos = :apellidos, 
                avatar = :avatar 
                WHERE id = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':nombre'    => $datos['nombre'],
            ':apellidos' => $datos['apellidos'],
            ':avatar'    => $datos['avatar'],
            ':id'        => $usuario['id']
        ]);
        return (int)$usuario['id'];
    } else {
        // 3. Insertar nuevo si no existe
        $sql = "INSERT INTO usuarios (google_id, email, nombre, apellidos, avatar, rol, activado) 
                VALUES (:google_id, :email, :nombre, :apellidos, :avatar, 'cliente', 1)";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':google_id' => $datos['google_id'],
            ':email'     => $datos['email'],
            ':nombre'    => $datos['nombre'],
            ':apellidos' => $datos['apellidos'],
            ':avatar'    => $datos['avatar']
        ]);
        return (int)$this->bd->lastInsertId();
    }
}
}
