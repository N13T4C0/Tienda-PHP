<?php
namespace Controladores;

// cambio pedido maestra
use Core\BaseControlador;
use Lib\Sesion;
use Servicios\ProductoServicio;
use Servicios\CategoriaServicio;
use Utils\Paginador;

class ProductoControlador extends BaseControlador
{
    // Catalogo con soporte de filtro por categoria, busqueda y paginacion
    public function index($idCategoria = null): void
    {
        $servProd = new ProductoServicio();
        $servCat  = new CategoriaServicio();

        $categorias      = $servCat->listarTodas();
        $categoriaActiva = null;

        if ($idCategoria !== null && is_numeric($idCategoria)) {
            $todos = $servProd->obtenerCatalogo((int) $idCategoria);
            $categoriaActiva = (int) $idCategoria;
        } else {
            $todos = $servProd->obtenerCatalogo();
        }

        // Paginador: recibe todos los productos, cuantos mostrar por pagina y la pagina actual
        $paginador = new Paginador($todos, 8, (int) ($_GET['pagina'] ?? 1));
        $productos = $paginador->elementosPagina();

        $this->view('productos/catalogo', [
            'productos'       => $productos,
            'categorias'      => $categorias,
            'categoriaActiva' => $categoriaActiva,
            'paginador'       => $paginador,
        ]);
    }

    // Detalle de un producto
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

        $this->view('productos/detalle', [
            'producto' => $producto,
        ]);
    }

    // Busca productos por texto
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

        $this->view('productos/buscar', [
            'productos'       => $productos,
            'categorias'      => $categorias,
            'categoriaActiva' => $categoriaActiva,
            'texto'           => $texto,
        ]);
    }
}
