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

    private function hidratar(array $filas): array
    {
        return array_map(fn($fila) => Categoria::fromArray($fila), $filas);
    }

    public function obtenerTodas(): array
    {
        $sql = "SELECT * FROM categorias ORDER BY nombre ASC";
        return $this->hidratar($this->bd->query($sql)->fetchAll());
    }

    public function obtenerUna(int $id): ?Categoria
    {
        $stmt = $this->bd->prepare("SELECT * FROM categorias WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ? Categoria::fromArray($fila) : null;
    }

    public function contarTodas(): int
    {
        return (int) $this->bd->query("SELECT COUNT(*) FROM categorias")->fetchColumn();
    }

    public function insertar(array $datos): int
    {
        $stmt = $this->bd->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (:nom, :desc)");
        $stmt->execute([
            ':nom'  => $datos['nombre'],
            ':desc' => $datos['descripcion'] ?? ''
        ]);
        return (int) $this->bd->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $stmt = $this->bd->prepare("UPDATE categorias SET nombre = :nom, descripcion = :desc WHERE id = :id");
        return $stmt->execute([
            ':id'   => $id,
            ':nom'  => $datos['nombre'],
            ':desc' => $datos['descripcion'] ?? ''
        ]);
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->bd->prepare("DELETE FROM categorias WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function tieneProductos(int $id): bool
    {
        $stmt = $this->bd->prepare("SELECT COUNT(*) FROM productos WHERE categoria_id = :id");
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
