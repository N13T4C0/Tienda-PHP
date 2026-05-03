<?php
class PagoControlador
{
    /**
     * PayPal redirige aqui tras aprobar el pago.
     * Captura la orden y crea el pedido en BD.
     */
    public function exito(): void
    {
        $orderId = $_GET['token'] ?? null;

        if (!$orderId || $orderId !== ($_SESSION['paypal_order_id'] ?? '')) {
            header('Location: ' . URL_BASE . '/cesta');
            exit;
        }

        $paypal    = new Paypal();
        $resultado = $paypal->capturarOrden($orderId);

        if (($resultado['status'] ?? '') === 'COMPLETED') {
            $idUsuario  = $_SESSION['usuario']['id'];
            $datosEnvio = $_SESSION['pago_envio'];
            $cesta      = Cesta::contenido();
            $total      = Cesta::importeTotal();

            $modeloPedido = new Pedido();
            $modeloPedido->crearPedidoCompleto($idUsuario, $datosEnvio, $cesta, $total);

            Cesta::vaciar();
            unset($_SESSION['paypal_order_id'], $_SESSION['pago_envio']);

            header('Location: ' . URL_BASE . '/pago/gracias');
        } else {
            header('Location: ' . URL_BASE . '/pago/error');
        }
        exit;
    }

    public function cancelado(): void
    {
        require APP . '/Vistas/pago/cancelado.php';
    }

    public function gracias(): void
    {
        require APP . '/Vistas/pago/gracias.php';
    }

    public function error(): void
    {
        require APP . '/Vistas/pago/error.php';
    }
}
