<?php

namespace Repositorios;

use Lib\Conexion;

class PedidoRepositorio
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::abrir();
    }

    public function iniciarTransaccion(): void
    {
        $this->db->beginTransaction();
    }

    public function confirmar(): void
    {
        $this->db->commit();
    }

    public function cancelar(): void
    {
        $this->db->rollBack();
    }

    public function insertarPedido(int $idUsuario, float $total, array $envio): int
    {
        $sql = "INSERT INTO pedidos (usuario_id, importe_total, direccion, localidad, provincia, estado, fecha_pedido) 
                VALUES (?, ?, ?, ?, ?, 'pendiente', NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idUsuario, $total, $envio['direccion'], $envio['localidad'], $envio['provincia']]);
        return (int)$this->db->lastInsertId();
    }

    public function insertarLinea(int $idPedido, array $item): void
    {
        $sql = "INSERT INTO lineas_pedido (pedido_id, producto_id, nombre_producto, precio_unidad, unidades, subtotal) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $idPedido,
            $item['producto']->id,
            $item['producto']->nombre,
            $item['producto']->precio,
            $item['cantidad'],
            $item['subtotal']
        ]);
    }
}
