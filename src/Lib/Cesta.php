<?php

namespace Lib;

use Lib\Sesion;
use Repositorios\ProductoRepositorio;

class Cesta
{
    /* Devuelve la clave de sesion correcta segun si hay usuario logueado o no */
    private static function clave(): string
    {
        $usuario = Sesion::usuario();
        return $usuario ? 'cesta_u' . $usuario['id'] : 'cesta_invitado';
    }

    /* Inicializa la cesta si no existia */
    public static function preparar(): void
    {
        $clave = self::clave();
        if (!isset($_SESSION[$clave])) {
            $_SESSION[$clave] = [];
        }
    }

    /* Añade unidades de un producto a la cesta. Valida stock real contra el objeto Producto.
     */
    public static function meterProducto(int $idProducto, int $unidades = 1): array
    {
        self::preparar();
        $clave = self::clave();
        $repo = new ProductoRepositorio();
        $producto = $repo->buscarPorId($idProducto); // Devuelve objeto Producto o null

        if (!$producto) {
            return ['ok' => false, 'mensaje' => 'Producto no encontrado'];
        }

        $yaTenia = $_SESSION[$clave][$idProducto] ?? 0;
        $total   = $yaTenia + $unidades;

        // Acceso como objeto: $producto->stock
        if ($total > $producto->stock) {
            return ['ok' => false, 'mensaje' => 'No hay stock suficiente (quedan ' . $producto->stock . ')'];
        }

        $_SESSION[$clave][$idProducto] = $total;
        return ['ok' => true, 'mensaje' => 'Producto añadido a la cesta'];
    }

    /* Cambia las unidades de un producto en la cesta */
    public static function cambiarUnidades(int $idProducto, int $unidades): array
    {
        self::preparar();
        $clave = self::clave();

        if ($unidades <= 0) {
            return self::quitarProducto($idProducto);
        }

        $repo     = new ProductoRepositorio();
        $producto = $repo->buscarPorId($idProducto);

        if (!$producto) {
            return ['ok' => false, 'mensaje' => 'Producto no encontrado'];
        }

        if ($unidades > $producto->stock) {
            return ['ok' => false, 'mensaje' => 'Stock insuficiente'];
        }

        $_SESSION[$clave][$idProducto] = $unidades;
        return ['ok' => true, 'mensaje' => 'Cesta actualizada'];
    }

    /* Quita un producto de la cesta */
    public static function quitarProducto(int $idProducto): array
    {
        self::preparar();
        unset($_SESSION[self::clave()][$idProducto]);
        return ['ok' => true, 'mensaje' => 'Producto eliminado de la cesta'];
    }

    /* Vacía la cesta */
    public static function vaciar(): void
    {
        $_SESSION[self::clave()] = [];
    }

    /* Devuelve el contenido de la cesta con todos los datos del producto */
    public static function contenido(): array
    {
        self::preparar();
        $items = [];
        $repo  = new ProductoRepositorio();

        foreach ($_SESSION[self::clave()] as $idProducto => $unidades) {
            $producto = $repo->buscarPorId((int) $idProducto);
            if ($producto) {
                $items[] = [
                    'producto' => $producto,
                    'cantidad' => (int) $unidades,
                    // Acceso como objeto: $producto->precio
                    'subtotal' => (float) $producto->precio * (int) $unidades,
                ];
            }
        }
        return $items;
    }

    /* Número total de unidades en la cesta */
    public static function totalUnidades(): int
    {
        self::preparar();
        return array_sum($_SESSION[self::clave()]);
    }

    /* Importe total de la cesta */
    public static function importeTotal(): float
    {
        self::preparar();
        $total = 0.0;
        $repo  = new ProductoRepositorio();

        foreach ($_SESSION[self::clave()] as $idProducto => $unidades) {
            $producto = $repo->buscarPorId((int) $idProducto);
            if ($producto) {
                // Acceso como objeto: $producto->precio
                $total += (float) $producto->precio * (int) $unidades;
            }
        }
        return $total;
    }

    /* Comprueba si la cesta esta vacia */
    public static function vacia(): bool
    {
        self::preparar();
        return empty($_SESSION[self::clave()]);
    }

    /* Al hacer login: migra la cesta de invitado a la del usuario.
     */
    public static function migrarAlLogin(int $idUsuario): void
    {
        $claveInvitado = 'cesta_invitado';
        $claveUsuario  = 'cesta_u' . $idUsuario;

        if (empty($_SESSION[$claveInvitado])) {
            return;
        }

        if (!isset($_SESSION[$claveUsuario])) {
            $_SESSION[$claveUsuario] = [];
        }

        foreach ($_SESSION[$claveInvitado] as $idProducto => $unidades) {
            $yaHabia = $_SESSION[$claveUsuario][$idProducto] ?? 0;
            $_SESSION[$claveUsuario][$idProducto] = $yaHabia + $unidades;
        }

        unset($_SESSION[$claveInvitado]);
    }
}
