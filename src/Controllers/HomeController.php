<?php
namespace App\Controllers;

class HomeController {
    private $base_url;
    
    public function __construct() {
        // Calcular la URL base completa con protocolo y host
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        
        // Obtener el path del script
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $scriptDir = dirname($scriptName);
        
        // Si estamos en public/, quitar ese segmento para obtener la base del proyecto
        if (basename($scriptDir) === 'public') {
            $basePath = dirname($scriptDir);
        } else {
            $basePath = $scriptDir;
        }
        
        // Normalizar basePath
        if ($basePath === '/' || $basePath === '\\') {
            $basePath = '';
        }
        
        // Construir URL base completa
        $this->base_url = $protocol . '://' . $host . $basePath;
    }
    
    public function index() {
        $base_url = $this->base_url;
        require_once __DIR__ . '/../../views/home/index.php';
    }
    
    public function productos() {
        $base_url = $this->base_url;
        // Aquí se deberían cargar los productos desde la base de datos
        // Por ahora mostramos una vista básica
        require_once __DIR__ . '/../../views/home/productos.php';
    }
    
    public function verProducto($id) {
        $base_url = $this->base_url;
        // Aquí se debería cargar un producto específico
        require_once __DIR__ . '/../../views/home/producto_detalle.php';
    }
    
    public function misPedidos() {
        $base_url = $this->base_url;
        // Verificar si el usuario está logueado
        if (!isset($_SESSION['identity'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para ver tus pedidos';
            header('Location: ' . $base_url . '/login');
            exit;
        }
        require_once __DIR__ . '/../../views/home/mis_pedidos.php';
    }
}