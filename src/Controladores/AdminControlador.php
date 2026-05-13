<?php

namespace Controladores;

// cambio pedido maestra
use Core\BaseControlador;
use Lib\Sesion;
use Middleware\AdminMiddleware;
use Requests\CategoriaRequest;
use Requests\ProductoRequest;
use Servicios\CategoriaServicio;
use Servicios\PedidoServicio;
use Servicios\ProductoServicio;
use Servicios\UsuarioServicio;

class AdminControlador extends BaseControlador
{
    public function __construct()
    {
        AdminMiddleware::verificar();
    }

    // Panel principal
    public function index(): void
    {
        $servProd = new ProductoServicio();
        $servCat  = new CategoriaServicio();
        $servUser = new UsuarioServicio();
        $servPed  = new PedidoServicio();

        $totalProductos  = count($servProd->listarTodos());
        $totalCategorias = count($servCat->listarTodas());
        $totalUsuarios   = count($servUser->listarTodos());
        $totalPedidos    = count($servPed->listarTodos());

        $this->view('admin/panel');
    }

    // Lista todos los productos
    public function productos(): void
    {
        $servicio  = new ProductoServicio();
        $productos = $servicio->listarTodos();

        $this->view('admin/productos');
    }

    // Formulario para crear producto
    public function nuevoProducto(): void
    {
        $servCat    = new CategoriaServicio();
        $categorias = $servCat->listarTodas();
        $producto   = null;

        $this->view('admin/form_producto');
    }

    // Formulario para editar producto existente
    public function editarProducto($id = null): void
    {
        if (!is_numeric($id)) {
            Sesion::redirigir('admin/productos');
        }

        $servProd = new ProductoServicio();
        $producto = $servProd->obtenerUno((int) $id);

        if (!$producto) {
            Sesion::mensaje('error', 'Producto no encontrado');
            Sesion::redirigir('admin/productos');
        }

        $servCat    = new CategoriaServicio();
        $categorias = $servCat->listarTodas();

        $this->view('admin/form_producto');
    }

    // Guarda un producto nuevo o actualiza uno existente
    public function guardarProducto(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Sesion::redirigir('admin/productos');
        }

        $resultado = ProductoRequest::validar($_POST);
        $errores   = $resultado['errores'];
        $datos     = $resultado['datos'];

        if (!empty($_FILES['imagen']['name'])) {
            $nombreImagen = $this->subirImagen($_FILES['imagen']);

            if ($nombreImagen === null) {
                Sesion::mensaje('error', 'Imagen no valida (solo JPG, PNG, GIF, WEBP)');
                Sesion::redirigir('admin/productos');
            }

            $datos['imagen'] = $nombreImagen;
        }

        if ($errores) {
            Sesion::mensaje('error', implode('<br>', $errores));
            Sesion::redirigir('admin/productos');
        }

        $servicio = new ProductoServicio();
        $id       = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {
            $servicio->modificar($id, $datos);
            Sesion::mensaje('ok', 'Producto actualizado');
        } else {
            $servicio->crear($datos);
            Sesion::mensaje('ok', 'Producto creado');
        }

        Sesion::redirigir('admin/productos');
    }

    // Elimina un producto (o lo oculta si tiene pedidos)
    public function borrarProducto($id = null): void
    {
        if (!is_numeric($id)) {
            Sesion::redirigir('admin/productos');
        }

        $servicio = new ProductoServicio();
        $servicio->eliminar((int) $id);
        Sesion::mensaje('ok', 'Producto eliminado');
        Sesion::redirigir('admin/productos');
    }

    // Restaura un producto oculto
    public function restaurarProducto($id = null): void
    {
        if (!is_numeric($id)) {
            Sesion::redirigir('admin/productos');
        }

        $servicio = new ProductoServicio();
        $servicio->restaurar((int) $id);
        Sesion::mensaje('ok', 'Producto restaurado');
        Sesion::redirigir('admin/productos');
    }

    // Lista todas las categorias
    public function categorias(): void
    {
        $servicio   = new CategoriaServicio();
        $categorias = $servicio->listarTodas();
        $catEditar  = null;

        $this->view('admin/categorias');
    }

    // Carga la pagina de categorias con el formulario pre-relleno para editar
    public function editarCategoria($id = null): void
    {
        if (!is_numeric($id)) {
            Sesion::redirigir('admin/categorias');
        }

        $servicio  = new CategoriaServicio();
        $catEditar = $servicio->obtenerUna((int) $id);

        if (!$catEditar) {
            Sesion::mensaje('error', 'Categoria no encontrada');
            Sesion::redirigir('admin/categorias');
        }

        $categorias = $servicio->listarTodas();

        $this->view('admin/categorias');
    }

    // Guarda una categoria nueva o actualiza una existente
    public function guardarCategoria(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Sesion::redirigir('admin/categorias');
        }

        $id    = (int) ($_POST['id'] ?? 0);
        $datos = [
            'nombre'      => $_POST['nombre']      ?? '',
            'descripcion' => $_POST['descripcion'] ?? '',
        ];

        $resultado    = CategoriaRequest::validar($datos);
        $errores      = $resultado['errores'];
        $datosLimpios = $resultado['datos'];

        if ($errores) {
            Sesion::mensaje('error', implode('<br>', $errores));
            Sesion::redirigir('admin/categorias');
        }

        $servicio = new CategoriaServicio();

        if ($id > 0) {
            $servicio->modificar($id, $datosLimpios);
            Sesion::mensaje('ok', 'Categoria actualizada');
        } else {
            $servicio->crear($datosLimpios);
            Sesion::mensaje('ok', 'Categoria creada');
        }

        Sesion::redirigir('admin/categorias');
    }

    // Elimina una categoria si no tiene productos
    public function borrarCategoria($id = null): void
    {
        if (!is_numeric($id)) {
            Sesion::redirigir('admin/categorias');
        }

        $servicio = new CategoriaServicio();

        try {
            $servicio->eliminar((int) $id);
            Sesion::mensaje('ok', 'Categoria eliminada');
        } catch (\RuntimeException $e) {
            Sesion::mensaje('error', $e->getMessage());
        }

        Sesion::redirigir('admin/categorias');
    }

    // Lista todos los pedidos
    public function pedidos(): void
    {
        $servicio = new PedidoServicio();
        $pedidos  = $servicio->listarTodos();

        $this->view('admin/pedidos');
    }

    // Muestra el detalle de un pedido con sus lineas
    public function detallePedido($id = null): void
    {
        if (!is_numeric($id)) {
            Sesion::redirigir('admin/pedidos');
        }

        $servicio = new PedidoServicio();
        $pedido   = $servicio->obtenerUno((int) $id);

        if (!$pedido) {
            Sesion::mensaje('error', 'Pedido no encontrado');
            Sesion::redirigir('admin/pedidos');
        }

        $lineas = $servicio->obtenerLineas((int) $id);

        $this->view('admin/detalle_pedido');
    }

    // Lista todos los usuarios
    public function usuarios(): void
    {
        $servicio = new UsuarioServicio();
        $usuarios = $servicio->listarTodos();

        $this->view('admin/usuarios');
    }

    // Elimina un usuario
    public function borrarUsuario($id = null): void
    {
        if (!is_numeric($id)) {
            Sesion::redirigir('admin/usuarios');
        }

        $servicio = new UsuarioServicio();
        $servicio->eliminar((int) $id);
        Sesion::mensaje('ok', 'Usuario eliminado');
        Sesion::redirigir('admin/usuarios');
    }

    /** Sube la imagen al servidor. Devuelve el nombre del archivo o null si falla */
    private function subirImagen(array $archivo): ?string
    {
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $extensionesPermitidas, true)) {
            return null;
        }

        $carpetaDestino = PUBLICO . '/uploads/imagenes/';
        if (!is_dir($carpetaDestino)) {
            mkdir($carpetaDestino, 0755, true);
        }

        $nombreFinal = time() . '_' . uniqid() . '.' . $extension;
        $rutaFinal   = $carpetaDestino . $nombreFinal;

        if (!move_uploaded_file($archivo['tmp_name'], $rutaFinal)) {
            return null;
        }

        return $nombreFinal;
    }
}
