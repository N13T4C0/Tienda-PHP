<?php
namespace App\Repositories;

use Config\Database;
use App\Models\Producto;
use PDO;

class ProductoRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerTodos() {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                INNER JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.activo = 1 
                ORDER BY p.created_at DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, Producto::class);
    }

    public function obtenerTodosAdmin() {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                INNER JOIN categorias c ON p.categoria_id = c.id 
                ORDER BY p.created_at DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, Producto::class);
    }

    public function obtenerPorCategoria($categoriaId, $pagina = 1, $porPagina = 12) {
        $offset = ($pagina - 1) * $porPagina;
        
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                INNER JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.categoria_id = :categoria_id AND p.activo = 1 
                ORDER BY p.created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':categoria_id', $categoriaId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_CLASS, Producto::class);
    }

    public function contarPorCategoria($categoriaId) {
        $sql = "SELECT COUNT(*) as total FROM productos WHERE categoria_id = :categoria_id AND activo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':categoria_id' => $categoriaId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function buscarPorId($id) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                INNER JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchObject(Producto::class);
    }

    public function crear(Producto $producto) {
        $sql = "INSERT INTO productos (categoria_id, nombre, descripcion, precio, precio_oferta, stock, activo, imagen) 
                VALUES (:categoria_id, :nombre, :descripcion, :precio, :precio_oferta, :stock, :activo, :imagen)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':categoria_id' => $producto->categoria_id,
            ':nombre' => $producto->nombre,
            ':descripcion' => $producto->descripcion,
            ':precio' => $producto->precio,
            ':precio_oferta' => $producto->precio_oferta,
            ':stock' => $producto->stock,
            ':activo' => $producto->activo,
            ':imagen' => $producto->imagen
        ]);
        
        return $this->db->lastInsertId();
    }

    public function actualizar(Producto $producto) {
        $sql = "UPDATE productos SET 
                categoria_id = :categoria_id,
                nombre = :nombre,
                descripcion = :descripcion,
                precio = :precio,
                precio_oferta = :precio_oferta,
                stock = :stock,
                activo = :activo,
                imagen = :imagen
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $producto->id,
            ':categoria_id' => $producto->categoria_id,
            ':nombre' => $producto->nombre,
            ':descripcion' => $producto->descripcion,
            ':precio' => $producto->precio,
            ':precio_oferta' => $producto->precio_oferta,
            ':stock' => $producto->stock,
            ':activo' => $producto->activo,
            ':imagen' => $producto->imagen
        ]);
    }

    public function eliminar($id) {
        $sql = "DELETE FROM productos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function actualizarStock($id, $cantidad) {
        $sql = "UPDATE productos SET stock = stock - :cantidad WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':cantidad' => $cantidad
        ]);
    }
}