<?php

namespace Repositorios;

use Config\Conexion;
use PDO;

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
                (nombre, apellidos, email, clave, rol, activado, token_email, token_email_creado)
            VALUES
                (:nom, :ape, :email, :clave, :rol, :act, :tok, NOW())";

        $stmt = $this->bd->prepare($sql);

        // Asignación a variables
        $nom   = $datos['nombre'];
        $ape   = $datos['apellidos'] ?? '';
        $email = $datos['email'];
        $clave = $datos['clave'];
        $rol   = $datos['rol'] ?? 'cliente';
        $act   = $datos['activado'] ?? 0;
        $tok   = $datos['token_email'] ?? null;

        // Vinculación por referencia usando bindParam
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':ape', $ape, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':clave', $clave, PDO::PARAM_STR);
        $stmt->bindParam(':rol', $rol, PDO::PARAM_STR);
        $stmt->bindParam(':act', $act, PDO::PARAM_INT);
        $stmt->bindParam(':tok', $tok, PDO::PARAM_STR | PDO::PARAM_NULL);


        $stmt->execute();
        return (int) $this->bd->lastInsertId();
    }

    /** Busca un usuario por su email */
    public function encontrarPorEmail(string $email): ?array
    {
        $stmt = $this->bd->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    /** Busca un usuario por su id */
    public function encontrarPorId(int $id): ?array
    {
        $stmt = $this->bd->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    /** Busca un usuario por su token de activacion de email */
    public function encontrarPorToken(string $token): ?array
    {
        $sql = "SELECT * FROM usuarios 
            WHERE token_email = :tok 
              AND activado = 0 
              AND token_email_creado IS NOT NULL 
              AND token_email_creado >= (NOW() - INTERVAL 1 MINUTE)";

        $stmt = $this->bd->prepare($sql);

        $stmt->bindParam(':tok', $token, PDO::PARAM_STR);

        $stmt->execute();

        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    /** Activa la cuenta del usuario */
    public function activar(int $id): bool
    {
        $stmt = $this->bd->prepare(
            "UPDATE usuarios
            SET activado = 1, token_email = NULL, token_email_creado = NULL
            WHERE id = :id"
        );
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /** Devuelve todos los usuarios (para el panel admin) */
    public function obtenerTodos(): array
    {
        // Al no tener parámetros externos, se puede usar query directamente
        return $this->bd
            ->query("SELECT id, nombre, apellidos, email, rol, activado, fecha_alta
                    FROM usuarios ORDER BY fecha_alta DESC")
            ->fetchAll();
    }

    /** Elimina un usuario por su id */
    public function eliminar(int $id): bool
    {
        $stmt = $this->bd->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
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

        $gid   = $datos['google_id'];
        $email = $datos['email'];

        $stmt->bindParam(':gid', $gid, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $existente = $stmt->fetch();

        if ($existente) {
            $stmt = $this->bd->prepare(
                "UPDATE usuarios SET nombre = :nom, apellidos = :ape, avatar = :av
                WHERE id = :id"
            );

            $nom = $datos['nombre'];
            $ape = $datos['apellidos'];
            $av  = $datos['avatar'];
            $id  = (int) $existente['id'];

            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindParam(':ape', $ape, PDO::PARAM_STR);
            $stmt->bindParam(':av', $av, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();
            return $id;
        }

        $stmt = $this->bd->prepare(
            "INSERT INTO usuarios (google_id, email, nombre, apellidos, avatar, rol, activado)
            VALUES (:gid, :email, :nom, :ape, :av, 'cliente', 1)"
        );

        $g_id  = $datos['google_id'];
        $mail  = $datos['email'];
        $name  = $datos['nombre'];
        $last  = $datos['apellidos'];
        $pfp   = $datos['avatar'];

        $stmt->bindParam(':gid', $g_id, PDO::PARAM_STR);
        $stmt->bindParam(':email', $mail, PDO::PARAM_STR);
        $stmt->bindParam(':nom', $name, PDO::PARAM_STR);
        $stmt->bindParam(':ape', $last, PDO::PARAM_STR);
        $stmt->bindParam(':av', $pfp, PDO::PARAM_STR);

        $stmt->execute();
        return (int) $this->bd->lastInsertId();
    }
}
