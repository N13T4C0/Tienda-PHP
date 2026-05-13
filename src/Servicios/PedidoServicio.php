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
     * beginTransaction/commit/rollBack son metodos nativos de PDO: agrupan
     * varias queries en un bloque atomico que se confirma o deshace entero.
     */
    public function crearPedido(int $idUsuario, array $datosEnvio, array $cesta, float $total): int
    {
        $pdo = $this->repoPedido->conexion();

        try {
            // par q no guarde nda en la bd hasta q pidamos confirmacion
            $pdo->beginTransaction();

            // 1. Insertar la cabecera del pedido
            $idPedido = $this->repoPedido->insertarCabecera($idUsuario, $total, $datosEnvio);

            // 2. Insertar cada linea y descontar stock
            foreach ($cesta as $item) {
                $producto = $item['producto'];
                $unidades = (int) $item['cantidad'];

                $this->repoPedido->insertarLinea($idPedido, $producto, $unidades);
                // restar stock 
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

    /**
     * Devuelve TODOS los pedidos con datos del usuario (para el panel admin).
     */
    public function listarTodos(): array
    {
        return $this->repoPedido->obtenerTodos();
    }

    /**
     * Devuelve un pedido concreto por su id (con datos del usuario).
     */
    public function obtenerUno(int $idPedido): ?array
    {
        return $this->repoPedido->obtenerUno($idPedido);
    }
}
