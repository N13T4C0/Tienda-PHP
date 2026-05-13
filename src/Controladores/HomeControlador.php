<?php
namespace Controladores;

// cambio pedido maestra
use Core\BaseControlador;
use Servicios\ProductoServicio;
use Servicios\CategoriaServicio;

class HomeControlador extends BaseControlador
{
    // Pagina inicial: muestra los productos destacados
    public function index(): void
    {
        $servProd = new ProductoServicio();
        $servCat  = new CategoriaServicio();

        $productos  = $servProd->obtenerCatalogo();
        $categorias = $servCat->listarTodas();

        // Mostramos como destacados los 4 primeros
        $destacados = array_slice($productos, 0, 4);

        $this->view('home/inicio', [
            'productos'  => $productos,
            'categorias' => $categorias,
            'destacados' => $destacados,
        ]);
    }
}
