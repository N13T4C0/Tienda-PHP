<?php
namespace Repositorios;

use Config\Conexion;

/**
 * ProductoRepositorio
 *
 * Responsabilidad UNICA: ejecutar las consultas SQL
 * relacionadas con la tabla `productos`.
 */
class ProductoRepositorio
{
    private $bd;

    public function __construct()
    {
        $this->bd = Conexion::abrir();
    }

    /** Devuelve todos los productos visibles (catalogo publico) */
    public function obtenerVisibles(): array
    {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre
                FROM productos p
                INNER JOIN categorias c ON c.id = p.categoria_id
                WHERE p.visible = 1
                ORDER BY p.fecha_alta DESC";
        return $this->bd->query($sql)->fetchAll();
    }

    /** Devuelve TODOS los productos incluidos los no visibles (panel admin) */
    public function obtenerTodos(): array
    {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre
                FROM productos p
                INNER JOIN categorias c ON c.id = p.categoria_id
                ORDER BY p.fecha_alta DESC";
        return $this->bd->query($sql)->fetchAll();
    }

    /** Devuelve productos de una categoria concreta */
    public function obtenerPorCategoria(int $idCategoria): array
    {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre
                FROM productos p
                INNER JOIN categorias c ON c.id = p.categoria_id
                WHERE p.visible = 1 AND p.categoria_id = :cat
                ORDER BY p.fecha_alta DESC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':cat' => $idCategoria]);
        return $stmt->fetchAll();
    }

    /** Busca productos por texto en nombre o descripcion */
    public function buscarPorTexto(string $texto): array
    {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre
                FROM productos p
                INNER JOIN categorias c ON c.id = p.categoria_id
                WHERE p.visible = 1
                  AND (p.nombre LIKE :q OR p.descripcion LIKE :q)
                ORDER BY p.fecha_alta DESC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':q' => '%' . $texto . '%']);
        return $stmt->fetchAll();
    }

    /** Devuelve un producto por su id */
    public function obtenerUno(int $id): ?array
    {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre
                FROM productos p
                INNER JOIN categorias c ON c.id = p.categoria_id
                WHERE p.id = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    /** Inserta un producto nuevo y devuelve el id generado */
    public function insertar(array $datos): int
    {
        $sql = "INSERT INTO productos
                  (categoria_id, nombre, descripcion, precio, stock, imagen, visible)
                VALUES
                  (:cat, :nom, :desc, :pre, :sto, :img, :vis)";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':cat'  => $datos['categoria_id'],
            ':nom'  => $datos['nombre'],
            ':desc' => $datos['descripcion'],
            ':pre'  => $datos['precio'],
            ':sto'  => $datos['stock'],
            ':img'  => $datos['imagen'] ?: 'sin-imagen.svg',
            ':vis'  => $datos['visible'] ? 1 : 0,
        ]);
        return (int) $this->bd->lastInsertId();
    }

    /** Actualiza un producto existente */
    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE productos SET
                    categoria_id = :cat,
                    nombre       = :nom,
                    descripcion  = :desc,
                    precio       = :pre,
                    stock        = :sto,
                    imagen       = :img,
                    visible      = :vis
                WHERE id = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':id'   => $id,
            ':cat'  => $datos['categoria_id'],
            ':nom'  => $datos['nombre'],
            ':desc' => $datos['descripcion'],
            ':pre'  => $datos['precio'],
            ':sto'  => $datos['stock'],
            ':img'  => $datos['imagen'] ?: 'sin-imagen.svg',
            ':vis'  => $datos['visible'] ? 1 : 0,
        ]);
    }

    /** Elimina un producto por su id */
    public function eliminar(int $id): bool
    {
        $stmt = $this->bd->prepare("DELETE FROM productos WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /** Resta unidades del stock al confirmar un pedido */
    public function restarStock(int $id, int $unidades): bool
    {
        $stmt = $this->bd->prepare(
            "UPDATE productos SET stock = stock - :u WHERE id = :id AND stock >= :u2"
        );
        return $stmt->execute([':u' => $unidades, ':u2' => $unidades, ':id' => $id]);
    }
}
