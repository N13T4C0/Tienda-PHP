<?php

namespace Controladores;

use Lib\Pagina;
use Lib\Sesion;
use Servicios\ProductoServicio;
use Servicios\CategoriaServicio;
use Utils\Paginador;

class ProductoControlador
{
    public function index($idCategoria = null): void
    {
        $servProd = new ProductoServicio();
        $servCat  = new CategoriaServicio();

        $categorias      = $servCat->listarTodas();
        $categoriaActiva = null;

        if ($idCategoria !== null && is_numeric($idCategoria)) {
            $todos           = $servProd->listarTodos((int) $idCategoria);
            $categoriaActiva = (int) $idCategoria;
        } else {
            $todos = $servProd->listarTodos();
        }

        $paginador = new Paginador($todos, 8, (int) ($_GET['pagina'] ?? 1));
        $productos = $paginador->elementosPagina();

        Pagina::renderizar('productos/catalogo', compact(
            'categorias',
            'categoriaActiva',
            'productos',
            'paginador'
        ));
    }

    public function detalle($id = null): void
    {
        if (!is_numeric($id)) Sesion::redirigir('producto');

        $servicio = new ProductoServicio();
        $producto = $servicio->obtenerUno((int) $id);

        if (!$producto) {
            Sesion::mensaje('error', 'El producto no existe');
            Sesion::redirigir('producto');
        }

        Pagina::renderizar('productos/detalle', compact('producto'));
    }

    public function buscar(): void
    {
        $texto      = trim($_GET['q'] ?? '');
        $servCat    = new CategoriaServicio();
        $servProd   = new ProductoServicio();
        $categorias = $servCat->listarTodas();
        $categoriaActiva = null;
        $productos  = ($texto !== '') ? $servProd->listarTodos(null, $texto) : [];

        Pagina::renderizar('productos/buscar', compact(
            'categorias',
            'categoriaActiva',
            'productos',
            'texto'
        ));
    }
}
