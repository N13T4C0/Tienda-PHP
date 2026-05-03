<?php
namespace Middleware;

use Lib\Sesion;

// Comprueba que el usuario ha iniciado sesion
class AccesoMiddleware
{
    public static function verificar(): void
    {
        if (!Sesion::usuario()) {
            Sesion::mensaje('error', 'Debes iniciar sesion para acceder');
            Sesion::redirigir('auth/login');
        }
    }
}
