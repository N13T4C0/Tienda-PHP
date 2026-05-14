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

    /** @return int Total de productos en la base de datos */
    public function contarTodos(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM productos")->fetchColumn();
    }

    /** @return object[] Todos los productos con su categoría, ordenados por id DESC */
    public function obtenerTodos(): array
    {
        return $this->db->query(
            "SELECT p.*, c.nombre AS categoria_nombre
             FROM productos p
             INNER JOIN categorias c ON c.id = p.categoria_id
             ORDER BY p.id DESC"
        )->fetchAll(PDO::FETCH_OBJ);
    }

    /** @return object[] Productos visibles con su categoría, ordenados por id DESC */
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

    /**
     * @param int $idCategoria
     * @return object[]
     */
    public function obtenerPorCategoria(int $idCategoria): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.nombre AS categoria_nombre
             FROM productos p
             INNER JOIN categorias c ON c.id = p.categoria_id
             WHERE p.visible = 1 AND p.categoria_id = :cat
             ORDER BY p.id DESC"
        );
        $stmt->bindParam(':cat',$idCategoria,PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param string $texto Texto a buscar en nombre y descripción
     * @return object[]
     */
    public function buscarPorTexto(string $texto): array
    {
        $busqueda = '%' . $texto . '%';
        $stmt = $this->db->prepare(
            "SELECT p.*, c.nombre AS categoria_nombre
             FROM productos p
             INNER JOIN categorias c ON c.id = p.categoria_id
             WHERE p.visible = 1
               AND (p.nombre LIKE :b1 OR p.descripcion LIKE :b2)
             ORDER BY p.id DESC"
        );
        $stmt->bindParam(':b1',$busqueda,PDO::PARAM_STR);
        $stmt->bindParam(':b2',$busqueda,PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param int $id
     * @return object|null Producto con su categoría, o null si no existe
     */
    public function buscarPorId(int $id): ?object
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.nombre AS categoria_nombre
             FROM productos p
             INNER JOIN categorias c ON c.id = p.categoria_id
             WHERE p.id = :id"
        );
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_OBJ);
        return $res ?: null;
    }

    /**
     * @param array $d Datos del producto (categoria_id, nombre, descripcion, precio, stock, imagen, visible)
     * @return bool
     */
    public function guardar(array $d): bool
    {
        $cat  = (int) $d['categoria_id'];
        $nom  = $d['nombre'];
        $desc = $d['descripcion'];
        $prec = $d['precio'];
        $stk  = (int) $d['stock'];
        $img  = $d['imagen'] ?? null;
        $vis  = isset($d['visible']) ? (int) $d['visible'] : 1;
        $sql  = "INSERT INTO productos (categoria_id, nombre, descripcion, precio, stock, imagen, visible, fecha_alta)
                 VALUES (:cat, :nom, :desc, :prec, :stk, :img, :vis, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':cat',$cat,PDO::PARAM_INT);
        $stmt->bindParam(':nom',$nom,PDO::PARAM_STR);
        $stmt->bindParam(':desc',$desc,PDO::PARAM_STR);
        $stmt->bindParam(':prec',$prec);
        $stmt->bindParam(':stk',$stk,PDO::PARAM_INT);
        $stmt->bindParam(':img',$img,PDO::PARAM_STR);
        $stmt->bindParam(':vis',$vis,PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * @param int   $id
     * @param array $d Datos a actualizar
     * @return bool
     */
    public function actualizar(int $id, array $d): bool
    {
        $cat  = (int) $d['categoria_id'];
        $nom  = $d['nombre'];
        $desc = $d['descripcion'];
        $prec = $d['precio'];
        $stk  = (int) $d['stock'];
        $img  = $d['imagen'] ?? null;
        $vis  = isset($d['visible']) ? (int) $d['visible'] : 1;
        $sql  = "UPDATE productos SET categoria_id = :cat, nombre = :nom, descripcion = :desc, precio = :prec, stock = :stk, imagen = :img, visible = :vis WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':cat',$cat,PDO::PARAM_INT);
        $stmt->bindParam(':nom',$nom,PDO::PARAM_STR);
        $stmt->bindParam(':desc',$desc,PDO::PARAM_STR);
        $stmt->bindParam(':prec',$prec);
        $stmt->bindParam(':stk',$stk,PDO::PARAM_INT);
        $stmt->bindParam(':img',$img,PDO::PARAM_STR);
        $stmt->bindParam(':vis',$vis,PDO::PARAM_INT);
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        return $stmt->execute();
    }

    /** @param int $id Marca el producto como no visible (visible = 0) */
    public function borrarLogico(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE productos SET visible = 0 WHERE id = :id");
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        return $stmt->execute();
    }

    /** @param int $id Reactiva el producto poniendo stock = 10 */
    public function activar(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE productos SET stock = 10 WHERE id = :id");
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * @param int $id
     * @param int $cantidad Unidades a descontar del stock
     */
    public function descontarStock(int $id, int $cantidad): void
    {
        $stmt = $this->db->prepare("UPDATE productos SET stock = stock - :cant WHERE id = :id");
        $stmt->bindParam(':cant',$cantidad,PDO::PARAM_INT);
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        $stmt->execute();
    }

    /** @param int $id Restaura el producto como visible (visible = 1) */
    public function restaurar(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE productos SET visible = 1 WHERE id = :id");
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        return $stmt->execute();
    }
}
