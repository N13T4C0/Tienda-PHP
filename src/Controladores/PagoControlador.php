<?php
namespace Controladores;

use Lib\Sesion;
use Lib\Cesta;
use Servicios\PedidoServicio;
use Utils\Paypal;

class PagoControlador
{
    // PayPal redirige aqui cuando el usuario aprueba el pago
    // Capturamos el pago y si todo va bien creamos el pedido en la base de datos
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

            // El servicio gestiona la transaccion: cabecera + lineas + descuento de stock
            $servicio = new PedidoServicio();
            $servicio->crearPedido($idUsuario, $datosEnvio, $cesta, $total);

            Cesta::vaciar();
            unset($_SESSION['paypal_order_id'], $_SESSION['pago_envio']);

            Sesion::redirigir('pago/gracias');
        } else {
            Sesion::redirigir('pago/error');
        }
    }

    // El usuario cancelo el pago en PayPal
    public function cancelado(): void
    {
        require APP . '/Vistas/pago/cancelado.php';
    }

    // El pago se completo correctamente
    public function gracias(): void
    {
        require APP . '/Vistas/pago/gracias.php';
    }

    // Algo fue mal con el pago
    public function error(): void
    {
        require APP . '/Vistas/pago/error.php';
    }
}
