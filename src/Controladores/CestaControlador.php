<?php

/**
 * Controlador de la cesta de la compra.
 */
class CestaControlador
{
    /** Vista con el contenido de la cesta */
    public function index(): void
    {
        $items = Cesta::contenido();
        $total = Cesta::importeTotal();

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/cesta/ver.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    /** Anade un producto a la cesta (POST con id y cantidad) */
    public function anadir(): void
    {
        $id       = (int) ($_POST['id_producto'] ?? 0);
        $cantidad = max(1, (int) ($_POST['cantidad'] ?? 1));

        $resultado = Cesta::meterProducto($id, $cantidad);

        $esAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($esAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'ok'            => $resultado['ok'],
                'mensaje'       => $resultado['mensaje'],
                'totalUnidades' => Cesta::totalUnidades(),
            ]);
            exit;
        }

        Sesion::mensaje($resultado['ok'] ? 'ok' : 'error', $resultado['mensaje']);
        Sesion::redirigir('cesta');
    }

    /** Cambia la cantidad de un producto */
    public function actualizar(): void
    {
        $id       = (int) ($_POST['id_producto'] ?? 0);
        $cantidad = (int) ($_POST['cantidad']    ?? 0);

        $resultado = Cesta::cambiarUnidades($id, $cantidad);
        Sesion::mensaje($resultado['ok'] ? 'ok' : 'error', $resultado['mensaje']);
        Sesion::redirigir('cesta');
    }

    /** Quita un producto */
    public function quitar($id = null): void
    {
        if (!is_numeric($id)) {
            Sesion::redirigir('cesta');
        }
        $resultado = Cesta::quitarProducto((int) $id);
        Sesion::mensaje('ok', $resultado['mensaje']);
        Sesion::redirigir('cesta');
    }

    /** Vacia la cesta */
    public function vaciar(): void
    {
        Cesta::vaciar();
        Sesion::mensaje('info', 'Cesta vaciada');
        Sesion::redirigir('cesta');
    }

    /** Pagina de finalizar compra (formulario de envio) */
    public function finalizar(): void
    {
        Sesion::exigirLogin();
        if (Cesta::vacia()) {
            Sesion::mensaje('error', 'Tu cesta esta vacia');
            Sesion::redirigir('cesta');
        }

        $items = Cesta::contenido();
        $total = Cesta::importeTotal();

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/cesta/finalizar.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    /** Confirma la compra: crea el pedido en BD */
    public function confirmar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_BASE . '/cesta');
            exit;
        }

        $cesta = Cesta::contenido();
        if (empty($cesta)) {
            header('Location: ' . URL_BASE . '/cesta');
            exit;
        }

        // Guardamos dirección en sesión para usarla tras el pago
        $_SESSION['pago_envio'] = [
            'direccion' => trim($_POST['direccion'] ?? ''),
            'localidad' => trim($_POST['localidad'] ?? ''),
            'provincia' => trim($_POST['provincia'] ?? ''),
        ];

        $total = array_reduce($cesta, fn($c, $i) => $c + $i['subtotal'], 0);

        // Creamos la orden en PayPal y redirigimos
        $paypal = new Paypal();
        $orden  = $paypal->crearOrden($total);

        $_SESSION['paypal_order_id'] = $orden['id'];

        foreach ($orden['links'] as $link) {
            if ($link['rel'] === 'approve') {
                header('Location: ' . $link['href']);
                exit;
            }
        }

        // Si algo falla con PayPal
        header('Location: ' . URL_BASE . '/pago/error');
        exit;
    }
}
