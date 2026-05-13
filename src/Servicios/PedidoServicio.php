<?php

namespace Servicios;

use Repositorios\PedidoRepositorio;
use Repositorios\ProductoRepositorio;

class PedidoServicio
{
    private $pedidoRepo;
    private $prodRepo;

    public function __construct()
    {
        $this->pedidoRepo = new PedidoRepositorio();
        $this->prodRepo = new ProductoRepositorio();
    }

    public function crearPedido(int $idUsuario, array $datosEnvio, array $cesta, float $total): int
    {
        try {
            $this->pedidoRepo->iniciarTransaccion();

            $idPedido = $this->pedidoRepo->insertarPedido($idUsuario, $total, $datosEnvio);

            foreach ($cesta as $item) {
                $this->pedidoRepo->insertarLinea($idPedido, $item);
                $this->prodRepo->descontarStock($item['producto']->id, $item['cantidad']);
            }

            $this->pedidoRepo->confirmar();
            return $idPedido;
        } catch (\Exception $e) {
            $this->pedidoRepo->cancelar();
            throw $e;
        }
    }
}
