<?php
namespace App\Controllers;

use App\Services\CarritoService;

class CarritoController {
    private $service;

    public function __construct() {
        $this->service = new CarritoService();
    }

    public function index() {
        $carrito = $this->service->obtener();
        $total = $this->service->calcularTotal();
        $totalProductos = $this->service->contarProductos();
        
        require_once __DIR__ . '/../../views/carrito/index.php';
    }

    public function agregar() {
        if (!isset($_POST['producto_id']) || !isset($_POST['cantidad'])) {
            $_SESSION['error'] = 'Datos incompletos';
            header('Location: /productos');
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
        $referer = $_SERVER['HTTP_REFERER'] ?? '/carrito';
        header('Location: ' . $referer);
        exit;
    }

    public function actualizar() {
        if (!isset($_POST['producto_id']) || !isset($_POST['cantidad'])) {
            $_SESSION['error'] = 'Datos incompletos';
            header('Location: /carrito');
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

        header('Location: /carrito');
        exit;
    }

    public function eliminar() {
        if (!isset($_POST['producto_id'])) {
            $_SESSION['error'] = 'Producto no especificado';
            header('Location: /carrito');
            exit;
        }

        $productoId = (int)$_POST['producto_id'];
        $resultado = $this->service->eliminar($productoId);

        $_SESSION['success'] = $resultado['mensaje'];
        header('Location: /carrito');
        exit;
    }

    public function vaciar() {
        $resultado = $this->service->vaciar();
        $_SESSION['success'] = $resultado['mensaje'];
        header('Location: /carrito');
        exit;
    }
}