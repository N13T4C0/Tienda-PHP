<?php

namespace Lib;

class Sesion
{
    /** Guarda al usuario en sesion tras un login correcto */
    public static function iniciar($usuario): void
    {
        if (is_object($usuario)) {
            $_SESSION['usuario'] = [
                'id'        => $usuario->id,
                'nombre'    => $usuario->nombre,
                'apellidos' => $usuario->apellidos ?? '',
                'email'     => $usuario->email,
                'rol'       => $usuario->rol,
            ];
        } else {
            $_SESSION['usuario'] = $usuario;
        }
    }

    public static function cerrar(): void
    {
        unset($_SESSION['usuario']);
        unset($_SESSION['cesta_invitado']);
        // Borrar también cestas específicas de usuario si existen
    }

    public static function logeado(): bool
    {
        return isset($_SESSION['usuario']);
    }

    public static function esAdmin(): bool
    {
        return self::logeado() && $_SESSION['usuario']['rol'] === 'admin';
    }

    public static function usuario(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    public static function mensaje(string $tipo, string $texto): void
    {
        $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
    }

    public static function consumirMensaje(): ?array
    {
        if (!isset($_SESSION['flash'])) {
            return null;
        }
        $msg = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $msg;
    }

    public static function redirigir(string $ruta = ''): void
    {
        header('Location: ' . URL_BASE . '/' . ltrim($ruta, '/'));
        exit;
    }

    public static function exigirLogin(): void
    {
        if (!self::logeado()) {
            self::mensaje('error', 'Debes iniciar sesion para acceder');
            self::redirigir('auth/login');
        }
    }

    public static function exigirAdmin(): void
    {
        self::exigirLogin();
        if (!self::esAdmin()) {
            self::mensaje('error', 'No tienes permisos para esa pagina');
            self::redirigir('');
        }
    }
}
