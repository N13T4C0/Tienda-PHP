<?php
namespace App\Repositories;

use Config\Database;
use App\Models\Categoria;
use PDO;

class CategoriaRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerTodas() {
        $sql = "SELECT * FROM categorias ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, Categoria::class);
    }

    public function crear(Categoria $categoria) {
        $sql = "INSERT INTO categorias (nombre, descripcion) VALUES (:nombre, :descripcion)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre' => $categoria->nombre,
            ':descripcion' => $categoria->descripcion
        ]);
        return $this->db->lastInsertId();
    }

    public function buscarPorId($id) {
        $sql = "SELECT * FROM categorias WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchObject(Categoria::class);
    }

    public function actualizar(Categoria $categoria) {
        $sql = "UPDATE categorias SET nombre = :nombre, descripcion = :descripcion WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $categoria->id,
            ':nombre' => $categoria->nombre,
            ':descripcion' => $categoria->descripcion
        ]);
    }

    public function eliminar($id) {
        $sql = "DELETE FROM categorias WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function buscarPorNombre($nombre) {
        $sql = "SELECT * FROM categorias WHERE nombre = :nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nombre' => $nombre]);
        return $stmt->fetchObject(Categoria::class);
    }

    public function tieneProductos($id) {
        $sql = "SELECT COUNT(*) as total FROM productos WHERE categoria_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }
}