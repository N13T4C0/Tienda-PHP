<?php
namespace App\Middleware;

class AuthMiddleware {
    public static function verificar() {
        if (!isset($_SESSION['identity'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para acceder a esta página';
            // Calcular URL base correcta (quitando /public del path)
            $scriptName = $_SERVER['SCRIPT_NAME'];
            $scriptDir = dirname($scriptName);
            if (basename($scriptDir) === 'public') {
                $base_url = dirname($scriptDir);
            } else {
                $base_url = $scriptDir;
            }
            if ($base_url === '/' || $base_url === '\\') {
                $base_url = '';
            }
            header('Location: ' . $base_url . '/login');
            exit;
        }
    }
}