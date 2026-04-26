<?php
namespace App\Controllers;

class HomeController {
    public function index() {
        $base_url = dirname($_SERVER['SCRIPT_NAME']);
        require_once __DIR__ . '/../../views/home/index.php';
    }
    
    public function productos() {
        $base_url = dirname($_SERVER['SCRIPT_NAME']);
        // Aquí se deberían cargar los productos desde la base de datos
        // Por ahora mostramos una vista básica
        require_once __DIR__ . '/../../views/home/productos.php';
    }
    
    public function verProducto($id) {
        $base_url = dirname($_SERVER['SCRIPT_NAME']);
        // Aquí se debería cargar un producto específico
        require_once __DIR__ . '/../../views/home/producto_detalle.php';
    }
    
    public function misPedidos() {
        $base_url = dirname($_SERVER['SCRIPT_NAME']);
        // Verificar si el usuario está logueado
        if (!isset($_SESSION['identity'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para ver tus pedidos';
            header('Location: ' . $base_url . '/login');
            exit;
        }
        require_once __DIR__ . '/../../views/home/mis_pedidos.php';
    }
}