<?php
namespace Lib;

/**
 * Helper estatico para gestionar la sesion del usuario logeado
 * y los mensajes flash (alertas que solo se muestran una vez).
 */
class Sesion
{
    /** Guarda al usuario en sesion tras un login correcto */
    public static function iniciar(array $usuario): void
    {
        $_SESSION['usuario'] = [
            'id'       => $usuario['id'],
            'nombre'   => $usuario['nombre'],
            'apellidos' => $usuario['apellidos'] ?? '',
            'email'    => $usuario['email'],
            'rol'      => $usuario['rol'],
        ];
    }

    /** Cierra la sesion */
    public static function cerrar(): void
    {
        unset($_SESSION['usuario']);
        unset($_SESSION['cesta_invitado']);
    }

    /** ¿Hay alguien logeado? */
    public static function logeado(): bool
    {
        return isset($_SESSION['usuario']);
    }

    /** ¿El usuario logeado es administrador? */
    public static function esAdmin(): bool
    {
        return self::logeado() && $_SESSION['usuario']['rol'] === 'admin';
    }

    /** Devuelve el usuario en sesion o null */
    public static function usuario(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    /** Guarda un mensaje flash (se muestra una sola vez) */
    public static function mensaje(string $tipo, string $texto): void
    {
        $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
    }

    /** Recupera y elimina el mensaje flash */
    public static function consumirMensaje(): ?array
    {
        if (!isset($_SESSION['flash'])) {
            return null;
        }
        $msg = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $msg;
    }

    /** Redirige a una URL relativa al proyecto y para la ejecucion */
    public static function redirigir(string $ruta = ''): void
    {
        header('Location: ' . URL_BASE . '/' . ltrim($ruta, '/'));
        exit;
    }

    /** Si no hay usuario logeado, redirige al login */
    public static function exigirLogin(): void
    {
        if (!self::logeado()) {
            self::mensaje('error', 'Debes iniciar sesion para acceder');
            self::redirigir('auth/login');
        }
    }

    /** Si no es admin, lo manda al inicio con un mensaje */
    public static function exigirAdmin(): void
    {
        self::exigirLogin();
        if (!self::esAdmin()) {
            self::mensaje('error', 'No tienes permisos para esa pagina');
            self::redirigir('');
        }
    }
}
