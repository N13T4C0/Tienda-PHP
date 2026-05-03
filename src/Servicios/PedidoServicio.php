<?php
namespace Servicios;

use Repositorios\PedidoRepositorio;
use Repositorios\ProductoRepositorio;


class PedidoServicio
{
    private PedidoRepositorio   $repoPedido;
    private ProductoRepositorio $repoProducto;

    public function __construct()
    {
        $this->repoPedido   = new PedidoRepositorio();
        $this->repoProducto = new ProductoRepositorio();
    }

    /**
     * Crea un pedido completo dentro de una transaccion:
     * inserta cabecera, las lineas y descuenta el stock de cada producto.
     */
    public function crearPedido(int $idUsuario, array $datosEnvio, array $cesta, float $total): int
    {
        $pdo = $this->repoPedido->conexion();

        try {
            $pdo->beginTransaction();

            // 1. Insertar la cabecera del pedido
            $idPedido = $this->repoPedido->insertarCabecera($idUsuario, $total, $datosEnvio);

            // 2. Insertar cada linea y descontar stock
            foreach ($cesta as $item) {
                $producto = $item['producto'];
                $unidades = (int) $item['cantidad'];

                $this->repoPedido->insertarLinea($idPedido, $producto, $unidades);
                $this->repoProducto->restarStock((int) $producto['id'], $unidades);
            }

            $pdo->commit();
            return $idPedido;

        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Devuelve los pedidos de un usuario.
     */
    public function obtenerPorUsuario(int $idUsuario): array
    {
        return $this->repoPedido->obtenerPorUsuario($idUsuario);
    }

    /**
     * Devuelve las lineas de un pedido.
     */
    public function obtenerLineas(int $idPedido): array
    {
        return $this->repoPedido->obtenerLineas($idPedido);
    }
}
