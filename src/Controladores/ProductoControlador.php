<?php
/**
 * Controlador de productos (catalogo + detalle + busqueda).
 */
class ProductoControlador
{
    /** Lista todos los productos visibles, opcionalmente filtrados por categoria */
    public function index($idCategoria = null): void
    {
        $modeloProducto  = new Producto();
        $modeloCategoria = new Categoria();
        $categorias      = $modeloCategoria->listar();

        $categoriaActiva = null;
        if ($idCategoria !== null && is_numeric($idCategoria)) {
            $productos       = $modeloProducto->listarPorCategoria((int) $idCategoria);
            $categoriaActiva = (int) $idCategoria;
        } else {
            $productos = $modeloProducto->listar();
        }

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/productos/catalogo.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    /** Detalle de un producto */
    public function detalle($id = null): void
    {
        if (!is_numeric($id)) {
            Sesion::redirigir('producto');
        }

        $modelo   = new Producto();
        $producto = $modelo->obtenerUno((int) $id);
        if (!$producto) {
            Sesion::mensaje('error', 'El producto no existe');
            Sesion::redirigir('producto');
        }

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/productos/detalle.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    /** Buscador (recibe ?q=texto via GET) */
    public function buscar(): void
    {
        $texto = trim($_GET['q'] ?? '');

        $modeloCategoria = new Categoria();
        $categorias      = $modeloCategoria->listar();
        $categoriaActiva = null;

        $productos = [];
        if ($texto !== '') {
            $modelo    = new Producto();
            $productos = $modelo->buscar($texto);
        }

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/productos/buscar.php';
        require APP . '/Vistas/comunes/pie.php';
    }
}
