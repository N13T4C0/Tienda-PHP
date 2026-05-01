<?php
/**
 * Controlador del panel de administracion.
 * Todas las acciones requieren rol = admin.
 */
class AdminControlador
{
    public function __construct()
    {
        Sesion::exigirAdmin();
    }

    /** Panel principal del admin */
    public function index(): void
    {
        $modeloProducto  = new Producto();
        $modeloCategoria = new Categoria();
        $modeloUsuario   = new Usuario();

        $totalProductos  = count($modeloProducto->listarTodos());
        $totalCategorias = count($modeloCategoria->listar());
        $totalUsuarios   = count($modeloUsuario->listar());

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/admin/panel.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    // -------------------- PRODUCTOS --------------------

    public function productos(): void
    {
        $modelo    = new Producto();
        $productos = $modelo->listarTodos();

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/admin/productos.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    public function nuevoProducto(): void
    {
        $modeloCat = new Categoria();
        $categorias = $modeloCat->listar();
        $producto   = null; // formulario vacio

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/admin/form_producto.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    public function editarProducto($id = null): void
    {
        if (!is_numeric($id)) Sesion::redirigir('admin/productos');

        $modelo   = new Producto();
        $producto = $modelo->obtenerUno((int) $id);
        if (!$producto) {
            Sesion::mensaje('error', 'Producto no encontrado');
            Sesion::redirigir('admin/productos');
        }
        $modeloCat  = new Categoria();
        $categorias = $modeloCat->listar();

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/admin/form_producto.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    public function guardarProducto(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') Sesion::redirigir('admin/productos');

        $datos = [
            'categoria_id' => (int)    ($_POST['categoria_id'] ?? 0),
            'nombre'       => trim($_POST['nombre']             ?? ''),
            'descripcion'  => trim($_POST['descripcion']        ?? ''),
            'precio'       => (float)  ($_POST['precio']        ?? 0),
            'stock'        => (int)    ($_POST['stock']         ?? 0),
            'imagen'       => trim($_POST['imagen']             ?? ''),
            'visible'      => isset($_POST['visible']) ? 1 : 0,
        ];

        // Validaciones minimas
        if ($datos['nombre'] === '' || $datos['precio'] < 0 || $datos['categoria_id'] <= 0) {
            Sesion::mensaje('error', 'Revisa los datos del formulario');
            Sesion::redirigir('admin/productos');
        }

        $modelo = new Producto();
        $id     = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {
            $modelo->modificar($id, $datos);
            Sesion::mensaje('ok', 'Producto actualizado');
        } else {
            $modelo->guardar($datos);
            Sesion::mensaje('ok', 'Producto creado');
        }
        Sesion::redirigir('admin/productos');
    }

    public function borrarProducto($id = null): void
    {
        if (!is_numeric($id)) Sesion::redirigir('admin/productos');
        $modelo = new Producto();
        $modelo->borrar((int) $id);
        Sesion::mensaje('ok', 'Producto eliminado');
        Sesion::redirigir('admin/productos');
    }

    // -------------------- CATEGORIAS --------------------

    public function categorias(): void
    {
        $modelo     = new Categoria();
        $categorias = $modelo->listar();

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/admin/categorias.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    public function guardarCategoria(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') Sesion::redirigir('admin/categorias');

        $id    = (int) ($_POST['id'] ?? 0);
        $datos = [
            'nombre'      => trim($_POST['nombre']      ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
        ];
        if ($datos['nombre'] === '') {
            Sesion::mensaje('error', 'El nombre de la categoria es obligatorio');
            Sesion::redirigir('admin/categorias');
        }

        $modelo = new Categoria();
        if ($id > 0) {
            $modelo->modificar($id, $datos);
            Sesion::mensaje('ok', 'Categoria actualizada');
        } else {
            $modelo->guardar($datos);
            Sesion::mensaje('ok', 'Categoria creada');
        }
        Sesion::redirigir('admin/categorias');
    }

    public function borrarCategoria($id = null): void
    {
        if (!is_numeric($id)) Sesion::redirigir('admin/categorias');
        $modelo = new Categoria();
        if ($modelo->tieneProductos((int) $id)) {
            Sesion::mensaje('error', 'No se puede borrar: la categoria tiene productos');
            Sesion::redirigir('admin/categorias');
        }
        $modelo->borrar((int) $id);
        Sesion::mensaje('ok', 'Categoria eliminada');
        Sesion::redirigir('admin/categorias');
    }

    // -------------------- USUARIOS --------------------

    public function usuarios(): void
    {
        $modelo   = new Usuario();
        $usuarios = $modelo->listar();

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/admin/usuarios.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    public function borrarUsuario($id = null): void
    {
        if (!is_numeric($id)) {
            Sesion::redirigir('admin/usuarios');
        }

        // Evitamos que el admin se borre a si mismo
        if ((int) $id === (int) Sesion::usuario()['id']) {
            Sesion::mensaje('error', 'No puedes eliminar tu propio usuario');
            Sesion::redirigir('admin/usuarios');
        }

        $modelo = new Usuario();
        $modelo->eliminar((int) $id);
        Sesion::mensaje('ok', 'Usuario eliminado correctamente');
        Sesion::redirigir('admin/usuarios');
    }
}
