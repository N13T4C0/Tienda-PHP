<?php

namespace Controladores;

use Lib\Sesion;
use Lib\Cesta;
use Servicios\PedidoServicio;
use Utils\GeneradorFactura;
use Utils\Paypal;

class PagoControlador
{
    // PayPal redirige aqui cuando el usuario aprueba el pago
    public function exito(): void
    {
        $orderId = $_GET['token'] ?? null;

        if (!$orderId || $orderId !== ($_SESSION['paypal_order_id'] ?? '')) {
            Sesion::redirigir('cesta');
        }

        $paypal    = new Paypal();
        $resultado = $paypal->capturarOrden($orderId);

        if (($resultado['status'] ?? '') === 'COMPLETED') {
            $idUsuario  = $_SESSION['usuario']['id'];
            $datosEnvio = $_SESSION['pago_envio'];
            $cesta      = Cesta::contenido();
            $total      = Cesta::importeTotal();

            $servicio = new PedidoServicio();
            $idPedido = $servicio->crearPedido($idUsuario, $datosEnvio, $cesta, $total);

            // Guardamos el id en sesion para mostrarlo en la pagina de gracias
            $_SESSION['ultimo_pedido_id'] = $idPedido;

            Cesta::vaciar();
            unset($_SESSION['paypal_order_id'], $_SESSION['pago_envio']);

            Sesion::redirigir('pago/gracias');

        } else {
            Sesion::redirigir('pago/error');
        }
    }

    // El pago se completo correctamente
    public function gracias(): void
    {
        $idPedido = $_SESSION['ultimo_pedido_id'] ?? null;

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/pago/gracias.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    // Descarga la factura PDF del ultimo pedido
    public function factura(): void
    {
        Sesion::exigirLogin();

        $idPedido = $_SESSION['ultimo_pedido_id'] ?? null;

        if (!$idPedido) {
            Sesion::redirigir('');
        }

        $servicio = new PedidoServicio();
        $pedido   = $servicio->obtenerUno((int) $idPedido);
        $lineas   = $servicio->obtenerLineas((int) $idPedido);

        if (!$pedido) {
            Sesion::redirigir('');
        }

        GeneradorFactura::descargar($pedido, $lineas);
    }

    // El usuario cancelo el pago en PayPal
    public function cancelado(): void
    {
        require APP . '/Vistas/pago/cancelado.php';
    }

    // Algo fue mal con el pago
    public function error(): void
    {
        require APP . '/Vistas/pago/error.php';
    }
}
