<?php

namespace Controladores;

use Lib\Pagina;
use Servicios\ProductoServicio;
use Servicios\CategoriaServicio;

class HomeControlador
{
    private ProductoServicio $servProd;
    private CategoriaServicio $servCat;

    public function __construct()
    {
        $this->servProd = new ProductoServicio();
        $this->servCat  = new CategoriaServicio();
    }

    public function index(): void
    {
        $productos  = $this->servProd->listarTodos();
        $categorias = $this->servCat->listarTodas();
        $destacados = array_slice($productos, 0, 4);

        Pagina::renderizar('home/inicio', compact('productos', 'categorias', 'destacados'));
    }
}
