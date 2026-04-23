<?php
namespace App\Middleware;

class AdminMiddleware {
    public static function verificar() {
        if (!isset($_SESSION['identity'])) {
            $_SESSION['error'] = 'Debes iniciar sesión';
            header('Location: /login');
            exit;
        }

        if ($_SESSION['identity']->rol !== 'admin') {
            $_SESSION['error'] = 'No tienes permisos para acceder a esta página';
            header('Location: /');
            exit;
        }
    }
}