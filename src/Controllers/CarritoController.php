<?php
namespace App\Controllers;

use App\Services\CarritoService;

class CarritoController {
    private $service;
    private $base_url;

    public function __construct() {
        $this->service = new CarritoService();
        // Obtener la URL base correctamente
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $scriptDir = dirname($scriptName);
        $this->base_url = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;
    }

    public function index() {
        $carrito = $this->service->obtener();
        $total = $this->service->calcularTotal();
        $totalProductos = $this->service->contarProductos();
        
        require_once __DIR__ . '/../../views/carrito/index.php';
    }

    public function agregar() {
        $base_url = $this->base_url;
        if (!isset($_POST['producto_id']) || !isset($_POST['cantidad'])) {
            $_SESSION['error'] = 'Datos incompletos';
            header('Location: ' . $base_url . '/productos');
            exit;
        }

        $productoId = (int)$_POST['producto_id'];
        $cantidad = (int)$_POST['cantidad'];

        $resultado = $this->service->agregar($productoId, $cantidad);

        if (isset($resultado['error'])) {
            $_SESSION['error'] = $resultado['error'];
        } else {
            $_SESSION['success'] = $resultado['mensaje'];
        }

        // Redirigir a la página anterior o al carrito
        $referer = $_SERVER['HTTP_REFERER'] ?? $base_url . '/carrito';
        header('Location: ' . $referer);
        exit;
    }

    public function actualizar() {
        $base_url = $this->base_url;
        if (!isset($_POST['producto_id']) || !isset($_POST['cantidad'])) {
            $_SESSION['error'] = 'Datos incompletos';
            header('Location: ' . $base_url . '/carrito');
            exit;
        }

        $productoId = (int)$_POST['producto_id'];
        $cantidad = (int)$_POST['cantidad'];

        $resultado = $this->service->actualizar($productoId, $cantidad);

        if (isset($resultado['error'])) {
            $_SESSION['error'] = $resultado['error'];
        } else {
            $_SESSION['success'] = 'Carrito actualizado';
        }

        header('Location: ' . $base_url . '/carrito');
        exit;
    }

    public function eliminar() {
        $base_url = $this->base_url;
        if (!isset($_POST['producto_id'])) {
            $_SESSION['error'] = 'Producto no especificado';
            header('Location: ' . $base_url . '/carrito');
            exit;
        }

        $productoId = (int)$_POST['producto_id'];
        $resultado = $this->service->eliminar($productoId);

        $_SESSION['success'] = $resultado['mensaje'];
        header('Location: ' . $base_url . '/carrito');
        exit;
    }

    public function vaciar() {
        $base_url = $this->base_url;
        $resultado = $this->service->vaciar();
        $_SESSION['success'] = $resultado['mensaje'];
        header('Location: ' . $base_url . '/carrito');
        exit;
    }
}