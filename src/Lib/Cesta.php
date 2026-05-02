<?php
/**
 * Helper para gestionar el carrito de la compra (lo guardamos en sesion).
 *
 * Estructura en sesion:
 *   $_SESSION['cesta'] = [ id_producto => unidades, ... ]
 */
class Cesta
{
    /** Devuelve la clave de sesion correcta segun si hay usuario logueado o no */
    private static function clave(): string
    {
        $usuario = Sesion::usuario();
        return $usuario ? 'cesta_u' . $usuario['id'] : 'cesta_invitado';
    }

    /** Inicializa la cesta si no existia */
    public static function preparar(): void
    {
        $clave = self::clave();
        if (!isset($_SESSION[$clave])) {
            $_SESSION[$clave] = [];
        }
    }

    /** Anade unidades de un producto. Devuelve [ok=>bool, mensaje=>string] */
    public static function meterProducto(int $idProducto, int $unidades = 1): array
    {
        self::preparar();
        $clave    = self::clave();
        $modelo   = new Producto();
        $producto = $modelo->obtenerUno($idProducto);

        if (!$producto) {
            return ['ok' => false, 'mensaje' => 'Producto no encontrado'];
        }

        $yaTenia = $_SESSION[$clave][$idProducto] ?? 0;
        $total   = $yaTenia + $unidades;

        if ($total > (int) $producto['stock']) {
            return ['ok' => false, 'mensaje' => 'No hay stock suficiente (quedan ' . $producto['stock'] . ')'];
        }

        $_SESSION[$clave][$idProducto] = $total;
        return ['ok' => true, 'mensaje' => 'Producto anadido a la cesta'];
    }

    /** Cambia las unidades de un producto en la cesta */
    public static function cambiarUnidades(int $idProducto, int $unidades): array
    {
        self::preparar();
        $clave = self::clave();

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

        $_SESSION[$clave][$idProducto] = $unidades;
        return ['ok' => true, 'mensaje' => 'Cesta actualizada'];
    }

    /** Quita un producto de la cesta */
    public static function quitarProducto(int $idProducto): array
    {
        self::preparar();
        unset($_SESSION[self::clave()][$idProducto]);
        return ['ok' => true, 'mensaje' => 'Producto eliminado de la cesta'];
    }

    /** Vacia la cesta */
    public static function vaciar(): void
    {
        $_SESSION[self::clave()] = [];
    }

    /** Devuelve el contenido de la cesta con datos completos del producto */
    public static function contenido(): array
    {
        self::preparar();
        $items  = [];
        $modelo = new Producto();

        foreach ($_SESSION[self::clave()] as $idProducto => $unidades) {
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
        return array_sum($_SESSION[self::clave()]);
    }

    /** Importe total de la cesta */
    public static function importeTotal(): float
    {
        self::preparar();
        $total  = 0.0;
        $modelo = new Producto();

        foreach ($_SESSION[self::clave()] as $idProducto => $unidades) {
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
        self::preparar();
        return empty($_SESSION[self::clave()]);
    }

    /**
     * Al hacer login: migra la cesta de invitado a la del usuario.
     * Llamar justo despues de Sesion::iniciar()
     */
    public static function migrarAlLogin(int $idUsuario): void
    {
        $claveInvitado = 'cesta_invitado';
        $claveUsuario  = 'cesta_u' . $idUsuario;

        if (empty($_SESSION[$claveInvitado])) {
            return; // No habia cesta de invitado, nada que migrar
        }

        if (!isset($_SESSION[$claveUsuario])) {
            $_SESSION[$claveUsuario] = [];
        }

        // Fusiona: suma las unidades si el producto ya estaba en la cesta del usuario
        foreach ($_SESSION[$claveInvitado] as $idProducto => $unidades) {
            $yaHabia = $_SESSION[$claveUsuario][$idProducto] ?? 0;
            $_SESSION[$claveUsuario][$idProducto] = $yaHabia + $unidades;
        }

        // Borra la cesta de invitado
        unset($_SESSION[$claveInvitado]);
    }
}
