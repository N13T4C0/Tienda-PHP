<?php
namespace App\Middleware;

class AdminMiddleware {
    public static function verificar() {
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