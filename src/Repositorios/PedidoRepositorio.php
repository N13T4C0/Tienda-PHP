<?php

namespace Repositorios;

use Core\BaseRepositorio;
use PDO;

class PedidoRepositorio extends BaseRepositorio
{
    /**
     * Devuelve la conexion PDO para usarla en transacciones desde el Servicio.
     * Necesario para beginTransaction / commit / rollBack en PedidoServicio.
     */
    public function conexion(): PDO
    {
        return $this->bd;
    }

    /** Inserta la cabecera del pedido y devuelve su id */
    public function insertarCabecera(int $idUsuario, float $total, array $datosEnvio): int
    {
        $stmt = $this->bd->prepare(
            "INSERT INTO pedidos (usuario_id, importe_total, direccion, localidad, provincia)
             VALUES (:u, :t, :d, :l, :p)"
        );

        // bindParam vincula por referencia → usamos variables intermedias
        $u   = $idUsuario;
        $t   = $total;
        $dir = $datosEnvio['direccion'] ?? '';
        $loc = $datosEnvio['localidad'] ?? '';
        $pro = $datosEnvio['provincia'] ?? '';

        $stmt->bindParam(':u', $u, PDO::PARAM_INT);
        $stmt->bindParam(':t', $t);                   // float, sin forzar tipo
        $stmt->bindParam(':d', $dir, PDO::PARAM_STR);
        $stmt->bindParam(':l', $loc, PDO::PARAM_STR);
        $stmt->bindParam(':p', $pro, PDO::PARAM_STR);

        $stmt->execute();
        return (int) $this->bd->lastInsertId();
    }

    /** Inserta una linea de pedido */
    public function insertarLinea(int $idPedido, array $producto, int $unidades): void
    {
        $subtotal = $producto['precio'] * $unidades;

        $stmt = $this->bd->prepare(
            "INSERT INTO lineas_pedido
                (pedido_id, producto_id, nombre_producto, precio_unidad, unidades, subtotal)
             VALUES (:pe, :pr, :np, :pu, :un, :st)"
        );

        // Variables intermedias para bindParam (necesita referencias)
        $pe = $idPedido;
        $pr = (int) $producto['id'];
        $np = $producto['nombre'];
        $pu = $producto['precio'];
        $un = $unidades;
        $st = $subtotal;

        $stmt->bindParam(':pe', $pe, PDO::PARAM_INT);
        $stmt->bindParam(':pr', $pr, PDO::PARAM_INT);
        $stmt->bindParam(':np', $np, PDO::PARAM_STR);
        $stmt->bindParam(':pu', $pu);                 // float
        $stmt->bindParam(':un', $un, PDO::PARAM_INT);
        $stmt->bindParam(':st', $st);                 // float

        $stmt->execute();
    }

    /** Devuelve los pedidos de un usuario concreto */
    public function obtenerPorUsuario(int $idUsuario): array
    {
        $stmt = $this->bd->prepare(
            "SELECT * FROM pedidos WHERE usuario_id = :u ORDER BY fecha_pedido DESC"
        );

        $u = $idUsuario;
        $stmt->bindParam(':u', $u, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Devuelve TODOS los pedidos con el nombre del usuario (para el panel admin).
     * Hace un JOIN con la tabla usuarios para mostrar quien hizo cada pedido.
     */
    public function obtenerTodos(): array
    {
        $sql = "SELECT p.*, u.nombre AS usuario_nombre, u.email AS usuario_email
                FROM pedidos p
                INNER JOIN usuarios u ON u.id = p.usuario_id
                ORDER BY p.fecha_pedido DESC";

        return $this->bd->query($sql)->fetchAll();
    }

    /** Devuelve las lineas de un pedido concreto */
    public function obtenerLineas(int $idPedido): array
    {
        $stmt = $this->bd->prepare(
            "SELECT * FROM lineas_pedido WHERE pedido_id = :pe ORDER BY id ASC"
        );

        $pe = $idPedido;
        $stmt->bindParam(':pe', $pe, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /** Devuelve un pedido concreto por su id (con datos del usuario) */
    public function obtenerUno(int $idPedido): ?array
    {
        $stmt = $this->bd->prepare(
            "SELECT p.*, u.nombre AS usuario_nombre, u.email AS usuario_email
             FROM pedidos p
             INNER JOIN usuarios u ON u.id = p.usuario_id
             WHERE p.id = :id"
        );

        $id = $idPedido;
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $fila = $stmt->fetch();
        return $fila ?: null;
    }
}
