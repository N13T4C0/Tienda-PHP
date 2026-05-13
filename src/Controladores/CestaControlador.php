<?php

namespace Controladores;

use Lib\Pagina;
use Lib\Sesion;
use Lib\Cesta;
use Utils\Paypal;
use Middleware\AccesoMiddleware;

class CestaControlador
{
    public function index(): void
    {
        Pagina::renderizar('cesta/ver', [
            'items' => Cesta::contenido(),
            'total' => Cesta::importeTotal(),
        ]);
    }

    public function anadir(): void
    {
        $resultado = Cesta::meterProducto(
            (int) ($_POST['id_producto'] ?? 0),
            (int) ($_POST['cantidad'] ?? 1)
        );
        Sesion::mensaje($resultado['ok'] ? 'ok' : 'error', $resultado['mensaje']);
        Sesion::redirigir('cesta');
    }

    public function actualizar(): void
    {
        $resultado = Cesta::cambiarUnidades(
            (int) ($_POST['id_producto'] ?? 0),
            (int) ($_POST['cantidad'] ?? 0)
        );
        Sesion::mensaje($resultado['ok'] ? 'ok' : 'error', $resultado['mensaje']);
        Sesion::redirigir('cesta');
    }

    public function quitar($id = null): void
    {
        if (!isset($id) || !is_numeric($id)) Sesion::redirigir('cesta');
        $resultado = Cesta::quitarProducto((int) $id);
        Sesion::mensaje('ok', $resultado['mensaje']);
        Sesion::redirigir('cesta');
    }

    public function vaciar(): void
    {
        Cesta::vaciar();
        Sesion::mensaje('info', 'Cesta vaciada');
        Sesion::redirigir('cesta');
    }

    public function finalizar(): void
    {
        AccesoMiddleware::verificar();
        if (Cesta::vacia()) {
            Sesion::mensaje('error', 'Tu cesta está vacía');
            Sesion::redirigir('cesta');
        }
        Pagina::renderizar('cesta/finalizar', [
            'items' => Cesta::contenido(),
            'total' => Cesta::importeTotal(),
        ]);
    }

    public function confirmar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') Sesion::redirigir('cesta');

        $cesta = Cesta::contenido();
        if (empty($cesta)) Sesion::redirigir('cesta');

        $_SESSION['pago_envio'] = [
            'direccion' => trim($_POST['direccion'] ?? ''),
            'localidad' => trim($_POST['localidad'] ?? ''),
            'provincia' => trim($_POST['provincia'] ?? ''),
        ];

        $total = array_sum(array_column($cesta, 'subtotal'));

        $paypal = new Paypal();
        $orden  = $paypal->crearOrden($total);

        if (empty($orden)) Sesion::redirigir('pago/error');

        $_SESSION['paypal_order_id'] = $orden['id'];

        foreach ($orden['links'] as $enlace) {
            if ($enlace['rel'] === 'approve') {
                header('Location: ' . $enlace['href']);
                exit;
            }
        }

        Sesion::redirigir('pago/error');
    }
}
