<?php
/**
 * Modelo Categoria. Operaciones CRUD sobre la tabla `categorias`.
 */
class Categoria
{
    private $bd;

    public function __construct()
    {
        $this->bd = Conexion::abrir();
    }

    /** Devuelve todas las categorias ordenadas alfabeticamente */
    public function listar(): array
    {
        return $this->bd
            ->query("SELECT * FROM categorias ORDER BY nombre ASC")
            ->fetchAll();
    }

    /** Devuelve una categoria por su id */
    public function obtenerUna(int $id): ?array
    {
        $stmt = $this->bd->prepare("SELECT * FROM categorias WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    /** Inserta una categoria */
    public function guardar(array $datos): int
    {
        $stmt = $this->bd->prepare(
            "INSERT INTO categorias (nombre, descripcion) VALUES (:nom, :desc)"
        );
        $stmt->execute([
            ':nom'  => $datos['nombre'],
            ':desc' => $datos['descripcion'] ?? '',
        ]);
        return (int) $this->bd->lastInsertId();
    }

    /** Modifica una categoria */
    public function modificar(int $id, array $datos): bool
    {
        $stmt = $this->bd->prepare(
            "UPDATE categorias SET nombre = :nom, descripcion = :desc WHERE id = :id"
        );
        return $stmt->execute([
            ':id'   => $id,
            ':nom'  => $datos['nombre'],
            ':desc' => $datos['descripcion'] ?? '',
        ]);
    }

    /** Borra una categoria (solo si no tiene productos) */
    public function borrar(int $id): bool
    {
        $stmt = $this->bd->prepare("DELETE FROM categorias WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /** Comprueba si una categoria tiene productos asociados */
    public function tieneProductos(int $id): bool
    {
        $stmt = $this->bd->prepare(
            "SELECT COUNT(*) AS total FROM productos WHERE categoria_id = :id"
        );
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetch()['total'] > 0;
    }
}
