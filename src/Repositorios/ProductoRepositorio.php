<?php

namespace Repositorios;

use Lib\Conexion;
use PDO;

class ProductoRepositorio
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::abrir();
    }

    public function contarTodos(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM productos")->fetchColumn();
    }

    public function obtenerTodos(): array
    {
        return $this->db->query(
            "SELECT p.*, c.nombre AS categoria_nombre
             FROM productos p
             INNER JOIN categorias c ON c.id = p.categoria_id
             ORDER BY p.id DESC"
        )->fetchAll(PDO::FETCH_OBJ);
    }

    public function obtenerVisibles(): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.nombre AS categoria_nombre
             FROM productos p
             INNER JOIN categorias c ON c.id = p.categoria_id
             WHERE p.visible = 1
             ORDER BY p.id DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function obtenerPorCategoria(int $idCategoria): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.nombre AS categoria_nombre
             FROM productos p
             INNER JOIN categorias c ON c.id = p.categoria_id
             WHERE p.visible = 1 AND p.categoria_id = ?
             ORDER BY p.id DESC"
        );
        $stmt->execute([$idCategoria]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function buscarPorTexto(string $texto): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.nombre AS categoria_nombre
             FROM productos p
             INNER JOIN categorias c ON c.id = p.categoria_id
             WHERE p.visible = 1
               AND (p.nombre LIKE ? OR p.descripcion LIKE ?)
             ORDER BY p.id DESC"
        );
        $busqueda = '%' . $texto . '%';
        $stmt->execute([$busqueda, $busqueda]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function buscarPorId(int $id): ?object
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.nombre AS categoria_nombre
             FROM productos p
             INNER JOIN categorias c ON c.id = p.categoria_id
             WHERE p.id = ?"
        );
        $stmt->execute([$id]);
        $res = $stmt->fetch(PDO::FETCH_OBJ);
        return $res ?: null;
    }

    public function guardar(array $d): bool
    {
        $sql = "INSERT INTO productos (categoria_id, nombre, descripcion, precio, stock, imagen, visible, fecha_alta)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        return $this->db->prepare($sql)->execute([
            $d['categoria_id'],
            $d['nombre'],
            $d['descripcion'],
            $d['precio'],
            $d['stock'],
            $d['imagen'] ?? null,
            isset($d['visible']) ? (int)$d['visible'] : 1
        ]);
    }

    public function actualizar(int $id, array $d): bool
    {
        $sql = "UPDATE productos SET categoria_id = ?, nombre = ?, descripcion = ?, precio = ?, stock = ?, imagen = ?, visible = ? WHERE id = ?";
        return $this->db->prepare($sql)->execute([
            $d['categoria_id'],
            $d['nombre'],
            $d['descripcion'],
            $d['precio'],
            $d['stock'],
            $d['imagen'] ?? null,
            isset($d['visible']) ? (int)$d['visible'] : 1,
            $id
        ]);
    }

    public function borrarLogico(int $id): bool
    {
        return $this->db->prepare("UPDATE productos SET visible = 0 WHERE id = ?")->execute([$id]);
    }

    public function activar(int $id): bool
    {
        return $this->db->prepare("UPDATE productos SET stock = 10 WHERE id = ?")->execute([$id]);
    }

    public function descontarStock(int $id, int $cantidad): void
    {
        $this->db->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?")->execute([$cantidad, $id]);
    }

    public function restaurar(int $id): bool
    {
        return $this->db->prepare("UPDATE productos SET visible = 1 WHERE id = ?")->execute([$id]);
    }
}
