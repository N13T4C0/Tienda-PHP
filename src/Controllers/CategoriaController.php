<?php
namespace App\Controllers;

use App\Services\CategoriaService;
use App\Requests\CategoriaRequest;
use App\Middleware\AdminMiddleware;

class CategoriaController {
    private $service;

    public function __construct() {
        $this->service = new CategoriaService();
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
        
        $request = new CategoriaRequest();
        
        if (!$request->validar($_POST)) {
            $_SESSION['errores'] = $request->getErrores();
            $_SESSION['old'] = $_POST;
            header('Location: /admin/categorias/crear');
            exit;
        }

        $resultado = $this->service->crear($_POST);

        if (isset($resultado['error'])) {
            $_SESSION['error'] = $resultado['error'];
            $_SESSION['old'] = $_POST;
            header('Location: /admin/categorias/crear');
        } else {
            $_SESSION['success'] = 'Categoría creada correctamente';
            header('Location: /admin/categorias');
        }
        exit;
    }

    public function editar($id) {
        AdminMiddleware::verificar();
        
        $categoria = $this->service->buscarPorId($id);
        
        if (!$categoria) {
            $_SESSION['error'] = 'Categoría no encontrada';
            header('Location: /admin/categorias');
            exit;
        }

        require_once __DIR__ . '/../../views/categorias/editar.php';
    }

    public function actualizar($id) {
        AdminMiddleware::verificar();
        
        $request = new CategoriaRequest();
        
        if (!$request->validar($_POST)) {
            $_SESSION['errores'] = $request->getErrores();
            $_SESSION['old'] = $_POST;
            header('Location: /admin/categorias/editar/' . $id);
            exit;
        }

        $resultado = $this->service->actualizar($id, $_POST);

        if (isset($resultado['error'])) {
            $_SESSION['error'] = $resultado['error'];
            $_SESSION['old'] = $_POST;
            header('Location: /admin/categorias/editar/' . $id);
        } else {
            $_SESSION['success'] = 'Categoría actualizada correctamente';
            header('Location: /admin/categorias');
        }
        exit;
    }

    public function eliminar($id) {
        AdminMiddleware::verificar();
        
        $resultado = $this->service->eliminar($id);

        if (isset($resultado['error'])) {
            $_SESSION['error'] = $resultado['error'];
        } else {
            $_SESSION['success'] = 'Categoría eliminada correctamente';
        }

        header('Location: /admin/categorias');
        exit;
    }
}