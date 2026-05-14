<?php

namespace Repositorios;

use Lib\Conexion;
use Modelos\Categoria;
use PDO;

class CategoriaRepositorio
{
    private $bd;

    public function __construct()
    {
        $this->bd = Conexion::abrir();
    }

    /** @param array $filas Filas crudas de la BD @return Categoria[] */
    private function hidratar(array $filas): array
    {
        return array_map(fn($fila) => Categoria::fromArray($fila), $filas);
    }

    /** @return Categoria[] Todas las categorías ordenadas por nombre ASC */
    public function obtenerTodas(): array
    {
        $sql = "SELECT * FROM categorias ORDER BY nombre ASC";
        return $this->hidratar($this->bd->query($sql)->fetchAll());
    }

    /**
     * @param int $id
     * @return Categoria|null
     */
    public function obtenerUna(int $id): ?Categoria
    {
        $stmt = $this->bd->prepare("SELECT * FROM categorias WHERE id = :id");
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        $stmt->execute();
        $fila = $stmt->fetch();
        return $fila ? Categoria::fromArray($fila) : null;
    }

    /** @return int Total de categorías */
    public function contarTodas(): int
    {
        return (int) $this->bd->query("SELECT COUNT(*) FROM categorias")->fetchColumn();
    }

    /**
     * @param array $datos Datos de la nueva categoría (nombre, descripcion)
     * @return int ID de la categoría insertada
     */
    public function insertar(array $datos): int
    {
        $nom  = $datos['nombre'];
        $desc = $datos['descripcion'] ?? '';
        $stmt = $this->bd->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (:nom, :desc)");
        $stmt->bindParam(':nom',$nom,PDO::PARAM_STR);
        $stmt->bindParam(':desc',$desc,PDO::PARAM_STR);
        $stmt->execute();
        return (int) $this->bd->lastInsertId();
    }

    /**
     * @param int   $id
     * @param array $datos Datos a actualizar (nombre, descripcion)
     * @return bool
     */
    public function actualizar(int $id, array $datos): bool
    {
        $nom  = $datos['nombre'];
        $desc = $datos['descripcion'] ?? '';
        $stmt = $this->bd->prepare("UPDATE categorias SET nombre = :nom, descripcion = :desc WHERE id = :id");
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        $stmt->bindParam(':nom',$nom,PDO::PARAM_STR);
        $stmt->bindParam(':desc',$desc,PDO::PARAM_STR);
        return $stmt->execute();
    }

    /** @param int $id Elimina la categoría definitivamente */
    public function eliminar(int $id): bool
    {
        $stmt = $this->bd->prepare("DELETE FROM categorias WHERE id = :id");
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        return $stmt->execute();
    }

    /** @param int $id Comprueba si la categoría tiene productos asociados */
    public function tieneProductos(int $id): bool
    {
        $stmt = $this->bd->prepare("SELECT COUNT(*) FROM productos WHERE categoria_id = :id");
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }
}
