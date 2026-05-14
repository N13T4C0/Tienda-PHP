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

    /** @param array $filas Filas crudas de la BD @return Usuario[] */
    private function hidratar(array $filas): array
    {
        return array_map(fn($fila) => Usuario::fromArray($fila), $filas);
    }

    /**
     * @param string $email
     * @return Usuario|null
     */
    public function encontrarPorEmail(string $email): ?Usuario
    {
        $stmt = $this->bd->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email',$email,PDO::PARAM_STR);
        $stmt->execute();
        $fila = $stmt->fetch();
        return $fila ? Usuario::fromArray($fila) : null;
    }

    /**
     * @param int $id
     * @return Usuario|null
     */
    public function encontrarPorId(int $id): ?Usuario
    {
        $stmt = $this->bd->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        $stmt->execute();
        $fila = $stmt->fetch();
        return $fila ? Usuario::fromArray($fila) : null;
    }

    /**
     * @param string $token Token de verificación de email
     * @return Usuario|null
     */
    public function encontrarPorToken(string $token): ?Usuario
    {
        $sql = "SELECT * FROM usuarios
                WHERE token_email = :tok
                AND activado = 0
                AND token_email_creado >= (NOW() - INTERVAL 1 MINUTE)";
        $stmt = $this->bd->prepare($sql);
        $stmt->bindParam(':tok',$token,PDO::PARAM_STR);
        $stmt->execute();
        $fila = $stmt->fetch();
        return $fila ? Usuario::fromArray($fila) : null;
    }

    /**
     * @param array $datos Datos del nuevo usuario
     * @return int ID del usuario insertado
     */
    public function insertar(array $datos): int
    {
        $nom   = $datos['nombre'];
        $ape   = $datos['apellidos'] ?? '';
        $email = $datos['email'];
        $clave = $datos['clave'];
        $rol   = $datos['rol'] ?? 'cliente';
        $act   = (int) ($datos['activado'] ?? 0);
        $tok   = $datos['token_email'] ?? null;
        $sql   = "INSERT INTO usuarios (nombre, apellidos, email, clave, rol, activado, token_email, token_email_creado)
                  VALUES (:nom, :ape, :email, :clave, :rol, :act, :tok, NOW())";
        $stmt  = $this->bd->prepare($sql);
        $stmt->bindParam(':nom',$nom,PDO::PARAM_STR);
        $stmt->bindParam(':ape',$ape,PDO::PARAM_STR);
        $stmt->bindParam(':email',$email,PDO::PARAM_STR);
        $stmt->bindParam(':clave',$clave,PDO::PARAM_STR);
        $stmt->bindParam(':rol',$rol,PDO::PARAM_STR);
        $stmt->bindParam(':act',$act,PDO::PARAM_INT);
        $stmt->bindParam(':tok',$tok,PDO::PARAM_STR);
        $stmt->execute();
        return (int) $this->bd->lastInsertId();
    }

    /** @param int $id Activa la cuenta y limpia el token de email */
    public function activar(int $id): bool
    {
        $stmt = $this->bd->prepare("UPDATE usuarios SET activado = 1, token_email = NULL, token_email_creado = NULL WHERE id = :id");
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        return $stmt->execute();
    }

    /** @param string $email Elimina un usuario no activado para permitir un nuevo intento de registro */
    public function eliminarNoActivadoPorEmail(string $email): void
    {
        $stmt = $this->bd->prepare("DELETE FROM usuarios WHERE email = :email AND activado = 0");
        $stmt->bindParam(':email',$email,PDO::PARAM_STR);
        $stmt->execute();
    }

    /** @return Usuario[] Todos los usuarios ordenados por fecha de alta DESC */
    public function obtenerTodos(): array
    {
        $sql = "SELECT * FROM usuarios ORDER BY fecha_alta DESC";
        return $this->hidratar($this->bd->query($sql)->fetchAll());
    }

    /** @return int Total de usuarios registrados */
    public function contarTodos(): int
    {
        return (int) $this->bd->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    }

    /** @param int $id Elimina el usuario definitivamente */
    public function eliminar(int $id): bool
    {
        $stmt = $this->bd->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * @param array $datos Datos del perfil de Google (google_id, email, nombre, apellidos, avatar)
     * @return int ID del usuario existente o recién creado
     */
    public function guardarDesdeGoogle(array $datos): int
    {
        $gid   = $datos['google_id'];
        $email = $datos['email'];
        $nom   = $datos['nombre'];
        $ape   = $datos['apellidos'];
        $av    = $datos['avatar'];

        $stmt = $this->bd->prepare("SELECT id FROM usuarios WHERE google_id = :gid OR email = :email LIMIT 1");
        $stmt->bindParam(':gid',$gid,PDO::PARAM_STR);
        $stmt->bindParam(':email',$email,PDO::PARAM_STR);
        $stmt->execute();
        $existente = $stmt->fetch();

        if ($existente) {
            $eid  = (int) $existente['id'];
            $stmt = $this->bd->prepare("UPDATE usuarios SET nombre = :nom, apellidos = :ape, avatar = :av WHERE id = :id");
            $stmt->bindParam(':nom',$nom,PDO::PARAM_STR);
            $stmt->bindParam(':ape',$ape,PDO::PARAM_STR);
            $stmt->bindParam(':av',$av,PDO::PARAM_STR);
            $stmt->bindParam(':id',$eid,PDO::PARAM_INT);
            $stmt->execute();
            return $eid;
        }

        $sql  = "INSERT INTO usuarios (google_id, email, nombre, apellidos, avatar, rol, activado)
                 VALUES (:gid, :email, :nom, :ape, :av, 'cliente', 1)";
        $stmt = $this->bd->prepare($sql);
        $stmt->bindParam(':gid',$gid,PDO::PARAM_STR);
        $stmt->bindParam(':email',$email,PDO::PARAM_STR);
        $stmt->bindParam(':nom',$nom,PDO::PARAM_STR);
        $stmt->bindParam(':ape',$ape,PDO::PARAM_STR);
        $stmt->bindParam(':av',$av,PDO::PARAM_STR);
        $stmt->execute();
        return (int) $this->bd->lastInsertId();
    }
}
