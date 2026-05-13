<?php

namespace Repositorios;

use Config\Conexion;
use PDO;

class CategoriaRepositorio
{
    private $bd;

    public function __construct()
    {
        $this->bd = Conexion::abrir();
    }

    /** Devuelve todas las categorias ordenadas alfabeticamente */
    public function obtenerTodas(): array
    {
        // Al ser una consulta fija sin variables externas, query() es correcto
        return $this->bd
            ->query("SELECT * FROM categorias ORDER BY nombre ASC")
            ->fetchAll();
    }

    /** Devuelve una categoria por su id */
    public function obtenerUna(int $id): ?array
    {
        $stmt = $this->bd->prepare("SELECT * FROM categorias WHERE id = :id");

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    /** Inserta una categoria nueva */
    public function insertar(array $datos): int
    {
        $stmt = $this->bd->prepare(
            "INSERT INTO categorias (nombre, descripcion) VALUES (:nom, :desc)"
        );

        $nom  = $datos['nombre'];
        $desc = $datos['descripcion'] ?? '';

        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':desc', $desc, PDO::PARAM_STR);

        $stmt->execute();
        return (int) $this->bd->lastInsertId();
    }

    /** Actualiza una categoria existente */
    public function actualizar(int $id, array $datos): bool
    {
        $stmt = $this->bd->prepare(
            "UPDATE categorias SET nombre = :nom, descripcion = :desc WHERE id = :id"
        );

        $nom  = $datos['nombre'];
        $desc = $datos['descripcion'] ?? '';
        $i    = $id;

        $stmt->bindParam(':id', $i, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':desc', $desc, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /** Elimina una categoria por su id */
    public function eliminar(int $id): bool
    {
        $stmt = $this->bd->prepare("DELETE FROM categorias WHERE id = :id");

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /** Comprueba si una categoria tiene productos asociados */
    public function tieneProductos(int $id): bool
    {
        $stmt = $this->bd->prepare(
            "SELECT COUNT(*) AS total FROM productos WHERE categoria_id = :id"
        );

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        $fila = $stmt->fetch();
        return (int) $fila['total'] > 0;
    }
}
