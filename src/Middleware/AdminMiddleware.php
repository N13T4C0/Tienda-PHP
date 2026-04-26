<?php
namespace App\Middleware;

class AdminMiddleware {
    public static function verificar() {
        $base_url = dirname($_SERVER['SCRIPT_NAME']);
        
        if (!isset($_SESSION['identity'])) {
            $_SESSION['error'] = 'Debes iniciar sesión';
            header('Location: ' . $base_url . '/login');
            exit;
        }

        if ($_SESSION['identity']->rol !== 'admin') {
            $_SESSION['error'] = 'No tienes permisos para acceder a esta página';
            header('Location: ' . $base_url . '/');
            exit;
        }
    }
}