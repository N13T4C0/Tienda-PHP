<?php

namespace Controladores;

use Lib\Pagina;
use Lib\Sesion;
use Lib\Cesta;
use Servicios\PedidoServicio;
use Utils\Paypal;

class PagoControlador
{
    public function exito(): void
    {
        $orderId = $_GET['token'] ?? null;

        if (!$orderId || $orderId !== ($_SESSION['paypal_order_id'] ?? '')) {
            Sesion::redirigir('cesta');
            return;
        }

        $paypal    = new Paypal();
        $resultado = $paypal->capturarOrden($orderId);

        if (($resultado['status'] ?? '') === 'COMPLETED') {
            try {
                $usuario = Sesion::usuario();
                if (!$usuario) throw new \Exception("Usuario no identificado");

                $datosEnvio = $_SESSION['pago_envio'] ?? [
                    'direccion' => 'No definida',
                    'localidad' => 'No definida',
                    'provincia' => 'No definida',
                ];

                $cesta = Cesta::contenido();
                $total = Cesta::importeTotal();

                if (empty($cesta)) throw new \Exception("Cesta vacía");

                (new PedidoServicio())->crearPedido($usuario['id'], $datosEnvio, $cesta, $total);

                Cesta::vaciar();
                unset($_SESSION['paypal_order_id'], $_SESSION['pago_envio']);
                Sesion::redirigir('pago/gracias');
            } catch (\Exception $e) {
                die("Error: " . $e->getMessage());
            }
        } else {
            Sesion::redirigir('pago/error');
        }
    }

    public function gracias(): void
    {
        Pagina::renderizar('pago/gracias');
    }

    public function error(): void
    {
        Pagina::renderizar('pago/error');
    }
}
