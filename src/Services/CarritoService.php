<?php
namespace App\Services;

use App\Repositories\ProductoRepository;

class CarritoService{
    private $productoRepository;

    public function __construct(){
        $this->productoRepository = new ProductoRepository();
        $this->inicializar();
    }

    public function inicializar(){
        if(!isset($_SESSION['carrito'])){
            $_SESSION['carrito'] = [];
        }
    }

    public function agregar($productoId, $cantidad = 1){
        //verificamos que el producto existe
        $producto = $this->productoRepository->buscarPorId($productoId);
        if(!$producto){
            return ['error' => 'Producto no encontrado'];
        }

        //verificar si hay stock
        $cantidadActual = $_SESSION['carrito'][$productoId] ?? 0;
        $cantidadTotal = $cantidadActual + $cantidad;

        if($cantidadTotal > $producto->stock){
            return ['error' => 'Stock insuficiente. Solo quedan ' . $producto->stock . ' unidades'];
        }

        //agregar 
        if (isset($_SESSION['carrito'][$productoId])) {
            $_SESSION['carrito'][$productoId] += $cantidad;
        } else {
            $_SESSION['carrito'][$productoId] = $cantidad;
        }

        return ['success' => true, 'mensaje' => 'Producto añadido al carrito'];
    }

    public function actualizar($productoId, $cantidad) {
        if ($cantidad <= 0) {
            return $this->eliminar($productoId);
        }

        // Verificar stock
        $producto = $this->productoRepository->buscarPorId($productoId);
        
        if ($cantidad > $producto->stock) {
            return ['error' => 'Stock insuficiente'];
        }

        $_SESSION['carrito'][$productoId] = $cantidad;
        return ['success' => true];
    }

    public function eliminar($productoId) {
        if (isset($_SESSION['carrito'][$productoId])) {
            unset($_SESSION['carrito'][$productoId]);
        }
        return ['success' => true, 'mensaje' => 'Producto eliminado'];
    }


    public function vaciar() {
        $_SESSION['carrito'] = [];
        return ['success' => true, 'mensaje' => 'Carrito vaciado'];
    }

    public function obtener() {
        $carrito = [];
        
        foreach ($_SESSION['carrito'] as $productoId => $cantidad) {
            $producto = $this->productoRepository->buscarPorId($productoId);
            
            if ($producto) {
                $carrito[] = [
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'subtotal' => $producto->precio * $cantidad
                ];
            }
        }

        return $carrito;
    }

    public function contarProductos() {
        return array_sum($_SESSION['carrito']);
    }

    public function calcularTotal() {
        $total = 0;
        
        foreach ($_SESSION['carrito'] as $productoId => $cantidad) {
            $producto = $this->productoRepository->buscarPorId($productoId);
            if ($producto) {
                $total += $producto->precio * $cantidad;
            }
        }

        return $total;
    }

    public function estaVacio() {
        return empty($_SESSION['carrito']);
    }
}