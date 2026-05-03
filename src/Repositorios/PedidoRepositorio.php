<?php
namespace Repositorios;

use Config\Conexion;

/**
 * PedidoRepositorio
 *
 * Responsabilidad UNICA: ejecutar las consultas SQL
 * relacionadas con las tablas `pedidos` y `lineas_pedido`.
 * La transaccion se gestiona desde PedidoServicio.
 */
class PedidoRepositorio
{
    private $bd;

    public function __construct()
    {
        $this->bd = Conexion::abrir();
    }

    /** Devuelve la conexion PDO (necesaria para gestionar transacciones desde el servicio) */
    public function conexion(): \PDO
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
        $stmt->execute([
            ':u' => $idUsuario,
            ':t' => $total,
            ':d' => $datosEnvio['direccion'] ?? '',
            ':l' => $datosEnvio['localidad'] ?? '',
            ':p' => $datosEnvio['provincia'] ?? '',
        ]);
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
        $stmt->execute([
            ':pe' => $idPedido,
            ':pr' => $producto['id'],
            ':np' => $producto['nombre'],
            ':pu' => $producto['precio'],
            ':un' => $unidades,
            ':st' => $subtotal,
        ]);
    }

    /** Devuelve los pedidos de un usuario concreto */
    public function obtenerPorUsuario(int $idUsuario): array
    {
        $stmt = $this->bd->prepare(
            "SELECT * FROM pedidos WHERE usuario_id = :u ORDER BY fecha_pedido DESC"
        );
        $stmt->execute([':u' => $idUsuario]);
        return $stmt->fetchAll();
    }

    /** Devuelve las lineas de un pedido concreto */
    public function obtenerLineas(int $idPedido): array
    {
        $stmt = $this->bd->prepare(
            "SELECT * FROM lineas_pedido WHERE pedido_id = :pe ORDER BY id ASC"
        );
        $stmt->execute([':pe' => $idPedido]);
        return $stmt->fetchAll();
    }
}
