<?php
namespace Middleware;

use Lib\Sesion;

// Comprueba que el usuario es administrador
class AdminMiddleware
{
    public static function verificar(): void
    {
        $usuario = Sesion::usuario();
        if (!$usuario || $usuario['rol'] !== 'admin') {
            Sesion::mensaje('error', 'No tienes permiso para acceder aqui');
            Sesion::redirigir('');
        }
    }
}
