<?php
namespace Controladores;

use Lib\Sesion;
use Lib\Cesta;
use Utils\Paypal;
use Middleware\AccesoMiddleware;

class CestaControlador
{
    // Muestra la cesta con todos los productos y el total
    public function index(): void
    {
        $items = Cesta::contenido();
        $total = Cesta::importeTotal();

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/cesta/ver.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    // Añade un producto a la cesta
    // Usamos ?? 0 en vez de isset() porque el id llega por POST, si no viene se queda en 0
    public function anadir(): void
    {
        $id = (int) ($_POST['id_producto'] ?? 0);
        $cantidad = (int) ($_POST['cantidad'] ?? 1);

        $resultado = Cesta::meterProducto($id, $cantidad);

        if ($resultado['ok']) {
            Sesion::mensaje('ok', $resultado['mensaje']);
        } else {
            Sesion::mensaje('error', $resultado['mensaje']);
        }

        Sesion::redirigir('cesta');
    }

    // Cambia la cantidad de un producto en la cesta
    public function actualizar(): void
    {
        $id = (int) ($_POST['id_producto'] ?? 0);
        $cantidad = (int) ($_POST['cantidad'] ?? 0);

        $resultado = Cesta::cambiarUnidades($id, $cantidad);

        if ($resultado['ok']) {
            Sesion::mensaje('ok', $resultado['mensaje']);
        } else {
            Sesion::mensaje('error', $resultado['mensaje']);
        }

        Sesion::redirigir('cesta');
    }

    // Elimina un producto de la cesta
    public function quitar($id = null): void
    {
        if (!isset($id) || !is_numeric($id)) {
            Sesion::redirigir('cesta');
        }

        $resultado = Cesta::quitarProducto((int) $id);
        Sesion::mensaje('ok', $resultado['mensaje']);
        Sesion::redirigir('cesta');
    }

    // Vacia toda la cesta
    public function vaciar(): void
    {
        Cesta::vaciar();
        Sesion::mensaje('info', 'Cesta vaciada');
        Sesion::redirigir('cesta');
    }

    // Muestra el formulario para introducir los datos de envio
    public function finalizar(): void
    {
        AccesoMiddleware::verificar();

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

    // Procesa el formulario de envio y redirige a PayPal para pagar
    public function confirmar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Sesion::redirigir('cesta');
        }

        $cesta = Cesta::contenido();
        if (empty($cesta)) {
            Sesion::redirigir('cesta');
        }

        // Guardamos los datos de envio en sesion para usarlos despues del pago
        $_SESSION['pago_envio'] = [
            'direccion' => trim($_POST['direccion'] ?? ''),
            'localidad' => trim($_POST['localidad'] ?? ''),
            'provincia' => trim($_POST['provincia'] ?? ''),
        ];

        // Calculamos el total del pedido
        $total = 0;
        foreach ($cesta as $item) {
            $total += $item['subtotal'];
        }

        // Creamos la orden en PayPal y redirigimos al usuario
        $paypal = new Paypal();
        $orden  = $paypal->crearOrden($total);

        if (empty($orden)) {
            Sesion::redirigir('pago/error');
        }

        $_SESSION['paypal_order_id'] = $orden['id'];

        // Buscamos el enlace de aprobacion que nos manda PayPal
        foreach ($orden['links'] as $enlace) {
            if ($enlace['rel'] === 'approve') {
                header('Location: ' . $enlace['href']);
                exit;
            }
        }

        Sesion::redirigir('pago/error');
    }
}
