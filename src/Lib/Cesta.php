<?php
/**
 * Helper para gestionar el carrito de la compra (lo guardamos en sesion).
 *
 * Estructura en sesion:
 *   $_SESSION['cesta'] = [ id_producto => unidades, ... ]
 */
class Cesta
{
    /** Inicializa la cesta si no existia */
    public static function preparar(): void
    {
        if (!isset($_SESSION['cesta'])) {
            $_SESSION['cesta'] = [];
        }
    }

    /** Anade unidades de un producto. Devuelve [ok=>bool, mensaje=>string] */
    public static function meterProducto(int $idProducto, int $unidades = 1): array
    {
        self::preparar();
        $modelo   = new Producto();
        $producto = $modelo->obtenerUno($idProducto);
        if (!$producto) {
            return ['ok' => false, 'mensaje' => 'Producto no encontrado'];
        }

        $yaTenia = $_SESSION['cesta'][$idProducto] ?? 0;
        $total   = $yaTenia + $unidades;

        if ($total > (int) $producto['stock']) {
            return ['ok' => false, 'mensaje' => 'No hay stock suficiente (quedan ' . $producto['stock'] . ')'];
        }

        $_SESSION['cesta'][$idProducto] = $total;
        return ['ok' => true, 'mensaje' => 'Producto anadido a la cesta'];
    }

    /** Cambia las unidades de un producto en la cesta */
    public static function cambiarUnidades(int $idProducto, int $unidades): array
    {
        self::preparar();
        if ($unidades <= 0) {
            return self::quitarProducto($idProducto);
        }

        $modelo   = new Producto();
        $producto = $modelo->obtenerUno($idProducto);
        if (!$producto) {
            return ['ok' => false, 'mensaje' => 'Producto no encontrado'];
        }
        if ($unidades > (int) $producto['stock']) {
            return ['ok' => false, 'mensaje' => 'Stock insuficiente'];
        }

        $_SESSION['cesta'][$idProducto] = $unidades;
        return ['ok' => true, 'mensaje' => 'Cesta actualizada'];
    }

    /** Quita un producto de la cesta */
    public static function quitarProducto(int $idProducto): array
    {
        self::preparar();
        unset($_SESSION['cesta'][$idProducto]);
        return ['ok' => true, 'mensaje' => 'Producto eliminado de la cesta'];
    }

    /** Vacia la cesta */
    public static function vaciar(): void
    {
        $_SESSION['cesta'] = [];
    }

    /** Devuelve el contenido de la cesta con datos completos del producto */
    public static function contenido(): array
    {
        self::preparar();
        $items   = [];
        $modelo  = new Producto();
        foreach ($_SESSION['cesta'] as $idProducto => $unidades) {
            $producto = $modelo->obtenerUno((int) $idProducto);
            if ($producto) {
                $items[] = [
                    'producto' => $producto,
                    'cantidad' => (int) $unidades,
                    'subtotal' => (float) $producto['precio'] * (int) $unidades,
                ];
            }
        }
        return $items;
    }

    /** Numero total de unidades en la cesta */
    public static function totalUnidades(): int
    {
        self::preparar();
        return array_sum($_SESSION['cesta']);
    }

    /** Importe total de la cesta */
    public static function importeTotal(): float
    {
        self::preparar();
        $total  = 0.0;
        $modelo = new Producto();
        foreach ($_SESSION['cesta'] as $idProducto => $unidades) {
            $producto = $modelo->obtenerUno((int) $idProducto);
            if ($producto) {
                $total += (float) $producto['precio'] * (int) $unidades;
            }
        }
        return $total;
    }

    /** ¿Esta vacia? */
    public static function vacia(): bool
    {
        return empty($_SESSION['cesta']);
    }
}
