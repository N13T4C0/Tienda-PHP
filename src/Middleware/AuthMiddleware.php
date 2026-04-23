<?php
namespace App\Middleware;

class AuthMiddleware {
    public static function verificar() {
        if (!isset($_SESSION['identity'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para acceder a esta página';
            header('Location: /login');
            exit;
        }
    }
}