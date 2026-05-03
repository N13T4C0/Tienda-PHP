<?php

/**
 * Modelo Pedido. Inserta cabeceras de pedido y sus lineas asociadas
 * usando una transaccion para garantizar consistencia.
 */
class Pedido
{
    private $bd;

    public function __construct()
    {
        $this->bd = Conexion::abrir();
    }

    /**
     * Inserta un pedido completo (cabecera + lineas) en una transaccion.
     *
     * @param int   $idUsuario
     * @param array $datosEnvio  ['direccion','localidad','provincia']
     * @param array $cesta       array de items con producto y cantidad
     * @param float $total
     * @return int  Id del pedido creado
     */
    public function crearPedidoCompleto(int $idUsuario, array $datosEnvio, array $cesta, float $total): int
    {
        try {
            $this->bd->beginTransaction();

            // Cabecera
            $stmt = $this->bd->prepare(
                "INSERT INTO pedidos (usuario_id, importe_total, direccion, localidad, provincia)
                 VALUES (:u, :t, :d, :l, :p)"
            );
            $stmt->execute([
                ':u' => $idUsuario,
                ':t' => $total,
                ':d' => $datosEnvio['direccion']  ?? '',
                ':l' => $datosEnvio['localidad']  ?? '',
                ':p' => $datosEnvio['provincia']  ?? '',
            ]);
            $idPedido = (int) $this->bd->lastInsertId();

            // Lineas
            $stmtLinea = $this->bd->prepare(
                "INSERT INTO lineas_pedido
                    (pedido_id, producto_id, nombre_producto, precio_unidad, unidades, subtotal)
                 VALUES (:pe, :pr, :np, :pu, :un, :st)"
            );
            $stmtStock = $this->bd->prepare(
                "UPDATE productos SET stock = stock - :u WHERE id = :id AND stock >= :u2"
            );

            foreach ($cesta as $item) {
                $producto = $item['producto'];
                $unidades = (int) $item['cantidad'];
                $subtotal = $producto['precio'] * $unidades;

                $stmtLinea->execute([
                    ':pe' => $idPedido,
                    ':pr' => $producto['id'],
                    ':np' => $producto['nombre'],
                    ':pu' => $producto['precio'],
                    ':un' => $unidades,
                    ':st' => $subtotal,
                ]);

                $stmtStock->execute([
                    ':u'  => $unidades,
                    ':u2' => $unidades,
                    ':id' => $producto['id'],
                ]);
            }

            $this->bd->commit();
            return $idPedido;
        } catch (Throwable $e) {
            $this->bd->rollBack();
            throw $e;
        }
    }

    /** Pedidos de un usuario concreto */
    public function listarPorUsuario(int $idUsuario): array
    {
        $stmt = $this->bd->prepare(
            "SELECT * FROM pedidos WHERE usuario_id = :u ORDER BY fecha_pedido DESC"
        );
        $stmt->execute([':u' => $idUsuario]);
        return $stmt->fetchAll();
    }

    /** Lineas de un pedido */
    public function lineasDelPedido(int $idPedido): array
    {
        $stmt = $this->bd->prepare(
            "SELECT * FROM lineas_pedido WHERE pedido_id = :p"
        );
        $stmt->execute([':p' => $idPedido]);
        return $stmt->fetchAll();
    }
}
