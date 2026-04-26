<?php
namespace App\Controllers;

use App\Services\CategoriaService;
use App\Requests\CategoriaRequest;
use App\Middleware\AdminMiddleware;

class CategoriaController {
    private $service;
    private $base_url;

    public function __construct() {
        $this->service = new CategoriaService();
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
        AdminMiddleware::verificar();
        
        $categorias = $this->service->obtenerTodas();
        require_once __DIR__ . '/../../views/categorias/index.php';
    }

    public function crear() {
        AdminMiddleware::verificar();
        
        require_once __DIR__ . '/../../views/categorias/crear.php';
    }

    public function guardar() {
        AdminMiddleware::verificar();
        $base_url = $this->base_url;
        
        $request = new CategoriaRequest();
        
        if (!$request->validar($_POST)) {
            $_SESSION['errores'] = $request->getErrores();
            $_SESSION['old'] = $_POST;
            header('Location: ' . $base_url . '/admin/categorias/crear');
            exit;
        }

        $resultado = $this->service->crear($_POST);

        if (isset($resultado['error'])) {
            $_SESSION['error'] = $resultado['error'];
            $_SESSION['old'] = $_POST;
            header('Location: ' . $base_url . '/admin/categorias/crear');
        } else {
            $_SESSION['success'] = 'Categoría creada correctamente';
            header('Location: ' . $base_url . '/admin/categorias');
        }
        exit;
    }

    public function editar($id) {
        AdminMiddleware::verificar();
        
        $categoria = $this->service->buscarPorId($id);
        
        if (!$categoria) {
            $base_url = $this->base_url;
            $_SESSION['error'] = 'Categoría no encontrada';
            header('Location: ' . $base_url . '/admin/categorias');
            exit;
        }

        require_once __DIR__ . '/../../views/categorias/editar.php';
    }

    public function actualizar($id) {
        AdminMiddleware::verificar();
        $base_url = $this->base_url;
        
        $request = new CategoriaRequest();
        
        if (!$request->validar($_POST)) {
            $_SESSION['errores'] = $request->getErrores();
            $_SESSION['old'] = $_POST;
            header('Location: ' . $base_url . '/admin/categorias/editar/' . $id);
            exit;
        }

        $resultado = $this->service->actualizar($id, $_POST);

        if (isset($resultado['error'])) {
            $_SESSION['error'] = $resultado['error'];
            $_SESSION['old'] = $_POST;
            header('Location: ' . $base_url . '/admin/categorias/editar/' . $id);
        } else {
            $_SESSION['success'] = 'Categoría actualizada correctamente';
            header('Location: ' . $base_url . '/admin/categorias');
        }
        exit;
    }

    public function eliminar($id) {
        AdminMiddleware::verificar();
        $base_url = $this->base_url;
        
        $resultado = $this->service->eliminar($id);

        if (isset($resultado['error'])) {
            $_SESSION['error'] = $resultado['error'];
        } else {
            $_SESSION['success'] = 'Categoría eliminada correctamente';
        }

        header('Location: ' . $base_url . '/admin/categorias');
        exit;
    }
}