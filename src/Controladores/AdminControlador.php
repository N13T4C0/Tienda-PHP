<?php
namespace Controladores;

use Lib\Sesion;
use Servicios\ProductoServicio;
use Servicios\CategoriaServicio;
use Servicios\UsuarioServicio;
use Middleware\AdminMiddleware;
use Requests\ProductoRequest;
use Requests\CategoriaRequest;

class AdminControlador
{
    // El constructor comprueba que el usuario es admin antes de cualquier accion
    public function __construct()
    {
        AdminMiddleware::verificar();
    }

    // Panel principal con el resumen de la tienda
    public function index(): void
    {
        $servProd = new ProductoServicio();
        $servCat  = new CategoriaServicio();
        $servUser = new UsuarioServicio();

        $totalProductos  = count($servProd->listarTodos());
        $totalCategorias = count($servCat->listarTodas());
        $totalUsuarios   = count($servUser->listarTodos());

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/admin/panel.php';
        require APP . '/Vistas/comunes/pie.php';
    }


    // Lista todos los productos
    public function productos(): void
    {
        $servicio  = new ProductoServicio();
        $productos = $servicio->listarTodos();

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/admin/productos.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    // Muestra el formulario para crear un producto nuevo
    public function nuevoProducto(): void
    {
        $servCat    = new CategoriaServicio();
        $categorias = $servCat->listarTodas();
        $producto   = null;

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/admin/form_producto.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    // Muestra el formulario para editar un producto existente
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

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/admin/form_producto.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    // Guarda un producto nuevo o actualiza uno existente
    // Gestiona tambien la subida del archivo de imagen
    public function guardarProducto(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Sesion::redirigir('admin/productos');
        }

        $datos = [
            'categoria_id' => (int)   ($_POST['categoria_id'] ?? 0),
            'nombre'       => trim($_POST['nombre']            ?? ''),
            'descripcion'  => trim($_POST['descripcion']       ?? ''),
            'precio'       => (float) ($_POST['precio']        ?? 0),
            'stock'        => (int)   ($_POST['stock']         ?? 0),
            'imagen'       => trim($_POST['imagen_actual']     ?? 'sin-imagen.svg'),
            'visible'      => isset($_POST['visible']) ? 1 : 0,
        ];

        // Procesamos la subida de imagen si el usuario selecciono un archivo
        if (!empty($_FILES['imagen']['name'])) {
            $nombreImagen = $this->subirImagen($_FILES['imagen']);

            if ($nombreImagen === null) {
                Sesion::mensaje('error', 'El archivo de imagen no es valido (solo JPG, PNG, GIF, WEBP)');
                Sesion::redirigir('admin/productos');
            }

            $datos['imagen'] = $nombreImagen;
        }

        $errores = ProductoRequest::validar($datos);
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

    // Elimina un producto
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


    // Lista todas las categorias
    public function categorias(): void
    {
        $servicio   = new CategoriaServicio();
        $categorias = $servicio->listarTodas();

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/admin/categorias.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    // Guarda una categoria nueva o actualiza una existente
    public function guardarCategoria(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Sesion::redirigir('admin/categorias');
        }

        $id    = (int) ($_POST['id'] ?? 0);
        $datos = [
            'nombre'      => trim($_POST['nombre']      ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
        ];

        $errores = CategoriaRequest::validar($datos);
        if ($errores) {
            Sesion::mensaje('error', implode('<br>', $errores));
            Sesion::redirigir('admin/categorias');
        }

        $servicio = new CategoriaServicio();

        if ($id > 0) {
            $servicio->modificar($id, $datos);
            Sesion::mensaje('ok', 'Categoria actualizada');
        } else {
            $servicio->crear($datos);
            Sesion::mensaje('ok', 'Categoria creada');
        }

        Sesion::redirigir('admin/categorias');
    }

    // Elimina una categoria si no tiene productos asociados
    public function borrarCategoria($id = null): void
    {
        if (!is_numeric($id)) {
            Sesion::redirigir('admin/categorias');
        }

        $servicio = new CategoriaServicio();

        try {
            // El servicio lanza una excepcion si la categoria tiene productos
            $servicio->eliminar((int) $id);
            Sesion::mensaje('ok', 'Categoria eliminada');
        } catch (\RuntimeException $e) {
            Sesion::mensaje('error', $e->getMessage());
        }

        Sesion::redirigir('admin/categorias');
    }


    // Lista todos los usuarios
    public function usuarios(): void
    {
        $servicio = new UsuarioServicio();
        $usuarios = $servicio->listarTodos();

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/admin/usuarios.php';
        require APP . '/Vistas/comunes/pie.php';
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


    /**
     * Sube la imagen al servidor y devuelve el nombre del archivo guardado.
     * Si el archivo no es valido devuelve null.
     * La imagen se renombra con time() para evitar colisiones de nombres.
     */
    private function subirImagen(array $archivo): ?string
    {
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension             = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $extensionesPermitidas, true)) {
            return null;
        }

        // Creamos la carpeta de destino si no existe
        $carpetaDestino = PUBLICO . '/uploads/imagenes/';
        if (!is_dir($carpetaDestino)) {
            mkdir($carpetaDestino, 0755, true);
        }

        // Renombramos con timestamp para evitar que dos imagenes con el mismo
        // nombre se sobreescriban
        $nombreFinal = time() . '_' . uniqid() . '.' . $extension;
        $rutaFinal   = $carpetaDestino . $nombreFinal;

        if (!move_uploaded_file($archivo['tmp_name'], $rutaFinal)) {
            return null;
        }

        return $nombreFinal;
    }
}
