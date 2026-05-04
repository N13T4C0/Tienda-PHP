<?php
namespace Controladores;

use Lib\Sesion;
use Servicios\ProductoServicio;
use Servicios\CategoriaServicio;
use Utils\Paginador;

class ProductoControlador
{
    /**
     * Catalogo con soporte de filtro por categoria, busqueda y paginacion.
     * Recibe ?pagina=N via GET para moverse entre paginas.
     */
    public function index($idCategoria = null): void
    {
        $servProd = new ProductoServicio();
        $servCat  = new CategoriaServicio();

        $categorias      = $servCat->listarTodas();
        $categoriaActiva = null;

        // Decidimos que productos cargar segun si hay filtro de categoria
        if ($idCategoria !== null && is_numeric($idCategoria)) {
            $todos           = $servProd->obtenerCatalogo((int) $idCategoria);
            $categoriaActiva = (int) $idCategoria;
        } else {
            $todos = $servProd->obtenerCatalogo();
        }

        // Paginador recibe todos los productos, cuantos mostrar por pagina (8) y la pagina actual
        // $_GET['pagina'] viene de la URL (?pagina=2), si no existe empieza en la 1
        // elementosPagina() devuelve solo los 8 productos que corresponden a esa pagina
        $paginador = new Paginador($todos, 8, (int) ($_GET['pagina'] ?? 1));
        $productos = $paginador->elementosPagina();

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

        $servicio = new ProductoServicio();
        $producto = $servicio->obtenerUno((int) $id);

        if (!$producto) {
            Sesion::mensaje('error', 'El producto no existe');
            Sesion::redirigir('producto');
        }

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/productos/detalle.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    public function buscar(): void
    {
        $texto = trim($_GET['q'] ?? '');

        $servCat         = new CategoriaServicio();
        $categorias      = $servCat->listarTodas();
        $categoriaActiva = null;

        $productos = [];
        if ($texto !== '') {
            $servProd  = new ProductoServicio();
            $productos = $servProd->obtenerCatalogo(null, $texto);
        }

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/productos/buscar.php';
        require APP . '/Vistas/comunes/pie.php';
    }
}
