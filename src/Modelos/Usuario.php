<?php
/**
 * Modelo Usuario. Maneja el registro, login y confirmacion de usuarios.
 */
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
            "SELECT * FROM usuarios WHERE token_email = :tok AND activado = 0"
        );
        $stmt->execute([':tok' => $token]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    /** Activa la cuenta del usuario (tras hacer click en el email) */
    public function activarCuenta(int $id): bool
    {
        $stmt = $this->bd->prepare(
            "UPDATE usuarios SET activado = 1, token_email = NULL WHERE id = :id"
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
}
