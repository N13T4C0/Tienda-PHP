<?php

namespace Repositorios;

use Lib\Conexion;
use PDO;

class PedidoRepositorio
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::abrir();
    }

    /** Inicia una transacción de base de datos */
    public function iniciarTransaccion(): void
    {
        $this->db->beginTransaction();
    }

    /** Confirma la transacción activa */
    public function confirmar(): void
    {
        $this->db->commit();
    }

    /** Cancela la transacción activa y revierte los cambios */
    public function cancelar(): void
    {
        $this->db->rollBack();
    }

    /**
     * @param int   $idUsuario
     * @param float $total     Importe total del pedido
     * @param array $envio     Datos de envío (direccion, localidad, provincia)
     * @return int ID del pedido insertado
     */
    public function insertarPedido(int $idUsuario, float $total, array $envio): int
    {
        $dir = $envio['direccion'];
        $loc = $envio['localidad'];
        $pro = $envio['provincia'];
        $sql = "INSERT INTO pedidos (usuario_id, importe_total, direccion, localidad, provincia, estado, fecha_pedido)
                VALUES (:u, :t, :d, :l, :p, 'pendiente', NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':u',$idUsuario,PDO::PARAM_INT);
        $stmt->bindParam(':t',$total);
        $stmt->bindParam(':d',$dir,PDO::PARAM_STR);
        $stmt->bindParam(':l',$loc,PDO::PARAM_STR);
        $stmt->bindParam(':p',$pro,PDO::PARAM_STR);
        $stmt->execute();
        return (int) $this->db->lastInsertId();
    }

    /**
     * @param int   $idPedido
     * @param array $item     Línea del carrito (producto, cantidad, subtotal)
     */
    public function insertarLinea(int $idPedido, array $item): void
    {
        $pid  = $item['producto']->id;
        $nom  = $item['producto']->nombre;
        $prec = $item['producto']->precio;
        $cant = $item['cantidad'];
        $sub  = $item['subtotal'];
        $sql  = "INSERT INTO lineas_pedido (pedido_id, producto_id, nombre_producto, precio_unidad, unidades, subtotal)
                 VALUES (:ped, :pid, :nom, :prec, :cant, :sub)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':ped',$idPedido,PDO::PARAM_INT);
        $stmt->bindParam(':pid',$pid,PDO::PARAM_INT);
        $stmt->bindParam(':nom',$nom,PDO::PARAM_STR);
        $stmt->bindParam(':prec',$prec);
        $stmt->bindParam(':cant',$cant,PDO::PARAM_INT);
        $stmt->bindParam(':sub',$sub);
        $stmt->execute();
    }
}
