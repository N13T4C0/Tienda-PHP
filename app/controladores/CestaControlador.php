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

        require APP . '/vistas/comunes/cabecera.php';
        require APP . '/vistas/cesta/ver.php';
        require APP . '/vistas/comunes/pie.php';
    }

    /** Anade un producto a la cesta (POST con id y cantidad) */
    public function anadir(): void
    {
        $id        = (int) ($_POST['id_producto'] ?? 0);
        $cantidad  = max(1, (int) ($_POST['cantidad'] ?? 1));

        $resultado = Cesta::meterProducto($id, $cantidad);
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

        require APP . '/vistas/comunes/cabecera.php';
        require APP . '/vistas/cesta/finalizar.php';
        require APP . '/vistas/comunes/pie.php';
    }

    /** Confirma la compra: crea el pedido en BD */
    public function confirmar(): void
    {
        Sesion::exigirLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || Cesta::vacia()) {
            Sesion::redirigir('cesta');
        }

        $datosEnvio = [
            'direccion' => trim($_POST['direccion'] ?? ''),
            'localidad' => trim($_POST['localidad'] ?? ''),
            'provincia' => trim($_POST['provincia'] ?? ''),
        ];

        if ($datosEnvio['direccion'] === '' || $datosEnvio['localidad'] === '') {
            Sesion::mensaje('error', 'Debes indicar la direccion completa');
            Sesion::redirigir('cesta/finalizar');
        }

        $items = Cesta::contenido();
        $total = Cesta::importeTotal();

        try {
            $modelo   = new Pedido();
            $idPedido = $modelo->crearPedidoCompleto(
                (int) Sesion::usuario()['id'],
                $datosEnvio,
                $items,
                $total
            );
            Cesta::vaciar();

            Sesion::mensaje('ok', 'Pedido #' . $idPedido . ' creado correctamente');
            Sesion::redirigir('');
        } catch (Throwable $e) {
            Sesion::mensaje('error', 'No se pudo procesar el pedido: ' . $e->getMessage());
            Sesion::redirigir('cesta/finalizar');
        }
    }
}
