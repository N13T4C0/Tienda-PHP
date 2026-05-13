<?php

namespace Repositorios;

use Lib\Conexion;
use PDO;

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

        $stmt->bindParam(':cat', $idCategoria, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Busca productos por texto en nombre o descripcion */
    public function buscarPorTexto(string $texto): array
    {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre
                FROM productos p
                INNER JOIN categorias c ON c.id = p.categoria_id
                WHERE p.visible = 1
                  AND (p.nombre LIKE :q1 OR p.descripcion LIKE :q2)
                ORDER BY p.fecha_alta DESC";
        $stmt = $this->bd->prepare($sql);

        // Preparamos el término de búsqueda para el LIKE
        $busqueda = '%' . $texto . '%';

        $stmt->bindParam(':q1', $busqueda, PDO::PARAM_STR);
        $stmt->bindParam(':q2', $busqueda, PDO::PARAM_STR);

        $stmt->execute();
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

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
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

        $cat   = $datos['categoria_id'];
        $nom   = $datos['nombre'];
        $desc  = $datos['descripcion'];
        $pre   = $datos['precio'];
        $sto   = $datos['stock'];
        $img   = $datos['imagen'] ?: 'sin-imagen.svg';
        $vis   = $datos['visible'] ? 1 : 0;

        $stmt->bindParam(':cat', $cat, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':desc', $desc, PDO::PARAM_STR);
        $stmt->bindParam(':pre', $pre); // Los decimales/floats es mejor vincularlos sin forzar STR o INT
        $stmt->bindParam(':sto', $sto, PDO::PARAM_INT);
        $stmt->bindParam(':img', $img, PDO::PARAM_STR);
        $stmt->bindParam(':vis', $vis, PDO::PARAM_INT);

        $stmt->execute();
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

        $cat   = $datos['categoria_id'];
        $nom   = $datos['nombre'];
        $desc  = $datos['descripcion'];
        $pre   = $datos['precio'];
        $sto   = $datos['stock'];
        $img   = $datos['imagen'] ?: 'sin-imagen.svg';
        $vis   = $datos['visible'] ? 1 : 0;

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':cat', $cat, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':desc', $desc, PDO::PARAM_STR);
        $stmt->bindParam(':pre', $pre);
        $stmt->bindParam(':sto', $sto, PDO::PARAM_INT);
        $stmt->bindParam(':img', $img, PDO::PARAM_STR);
        $stmt->bindParam(':vis', $vis, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /** Elimina un producto. Si tiene pedidos asociados, hace borrado lógico */
    public function eliminar(int $id): bool
    {
        try {
            $stmt = $this->bd->prepare("DELETE FROM productos WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\PDOException $e) {
            // Si falla por FK (tiene pedidos), borrado lógico
            if ($e->getCode() === '23000') {
                $stmt = $this->bd->prepare("UPDATE productos SET visible = 0 WHERE id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                return $stmt->execute();
            }
            throw $e; // Si es otro error, lo relanzamos
        }
    }

    public function restaurar(int $id): bool
    {
        $stmt = $this->bd->prepare("UPDATE productos SET visible = 1 WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /** Resta unidades del stock al confirmar un pedido */
    public function restarStock(int $id, int $unidades): bool
    {
        $stmt = $this->bd->prepare(
            "UPDATE productos SET stock = stock - :u WHERE id = :id AND stock >= :u2"
        );

        // Aquí es mejor usar variables para cumplir con la referencia de bindParam
        $u = $unidades;
        $i = $id;

        $stmt->bindParam(':u', $u, PDO::PARAM_INT);
        $stmt->bindParam(':u2', $u, PDO::PARAM_INT);
        $stmt->bindParam(':id', $i, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
