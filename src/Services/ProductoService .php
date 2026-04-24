<?php
namespace App\Services;

use App\Repositories\ProductoRepository;
use App\Models\Producto;

class ProductoService {
    private $repository;

    public function __construct() {
        $this->repository = new ProductoRepository();
    }

    public function obtenerTodos() {
        return $this->repository->obtenerTodos();
    }

    public function obtenerTodosAdmin() {
        return $this->repository->obtenerTodosAdmin();
    }

    public function obtenerPorCategoria($categoriaId, $pagina = 1, $porPagina = 12) {
        return $this->repository->obtenerPorCategoria($categoriaId, $pagina, $porPagina);
    }

    public function contarPorCategoria($categoriaId) {
        return $this->repository->contarPorCategoria($categoriaId);
    }

    public function buscarPorId($id) {
        return $this->repository->buscarPorId($id);
    }

    public function crear($datos, $archivo = null) {
        $producto = new Producto();
        $producto->categoria_id = $datos['categoria_id'];
        $producto->nombre = htmlspecialchars(trim($datos['nombre']));
        $producto->descripcion = htmlspecialchars(trim($datos['descripcion'] ?? ''));
        $producto->precio = $datos['precio'];
        $producto->precio_oferta = !empty($datos['precio_oferta']) ? $datos['precio_oferta'] : null;
        $producto->stock = $datos['stock'];
        $producto->activo = isset($datos['activo']) ? 1 : 0;
        
        // Gestión de imagen
        if ($archivo && $archivo['error'] === UPLOAD_ERR_OK) {
            $resultado = $this->subirImagen($archivo);
            if (isset($resultado['error'])) {
                return $resultado;
            }
            $producto->imagen = $resultado['nombre'];
        } else {
            $producto->imagen = null;
        }

        $id = $this->repository->crear($producto);
        return ['success' => true, 'id' => $id];
    }

    public function actualizar($id, $datos, $archivo = null) {
        $productoActual = $this->repository->buscarPorId($id);
        
        if (!$productoActual) {
            return ['error' => 'Producto no encontrado'];
        }

        $producto = new Producto();
        $producto->id = $id;
        $producto->categoria_id = $datos['categoria_id'];
        $producto->nombre = htmlspecialchars(trim($datos['nombre']));
        $producto->descripcion = htmlspecialchars(trim($datos['descripcion'] ?? ''));
        $producto->precio = $datos['precio'];
        $producto->precio_oferta = !empty($datos['precio_oferta']) ? $datos['precio_oferta'] : null;
        $producto->stock = $datos['stock'];
        $producto->activo = isset($datos['activo']) ? 1 : 0;
        
        // Gestión de imagen
        if ($archivo && $archivo['error'] === UPLOAD_ERR_OK) {
            // Eliminar imagen anterior si existe
            if ($productoActual->imagen) {
                $rutaAnterior = __DIR__ . '/../../public/uploads/productos/' . $productoActual->imagen;
                if (file_exists($rutaAnterior)) {
                    unlink($rutaAnterior);
                }
            }
            
            $resultado = $this->subirImagen($archivo);
            if (isset($resultado['error'])) {
                return $resultado;
            }
            $producto->imagen = $resultado['nombre'];
        } else {
            // Mantener imagen actual
            $producto->imagen = $productoActual->imagen;
        }

        $this->repository->actualizar($producto);
        return ['success' => true];
    }

    public function eliminar($id) {
        $producto = $this->repository->buscarPorId($id);
        
        if (!$producto) {
            return ['error' => 'Producto no encontrado'];
        }

        // Eliminar imagen si existe
        if ($producto->imagen) {
            $rutaImagen = __DIR__ . '/../../public/uploads/productos/' . $producto->imagen;
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            }
        }

        $this->repository->eliminar($id);
        return ['success' => true];
    }

    private function subirImagen($archivo) {
        // Validar tipo de archivo
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($archivo['type'], $tiposPermitidos)) {
            return ['error' => 'Tipo de archivo no permitido. Solo JPG, PNG, GIF, WEBP'];
        }

        // Validar tamaño (2MB máximo)
        if ($archivo['size'] > 2 * 1024 * 1024) {
            return ['error' => 'La imagen no puede superar los 2MB'];
        }

        // Crear carpeta si no existe
        $carpetaDestino = __DIR__ . '/../../public/uploads/productos/';
        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true);
        }

        // Generar nombre único
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = time() . '_' . uniqid() . '.' . $extension;
        $rutaCompleta = $carpetaDestino . $nombreArchivo;

        // Mover archivo
        if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            return ['error' => 'Error al subir la imagen'];
        }

        return ['nombre' => $nombreArchivo];
    }
}