<?php
namespace Controladores;

use Servicios\ProductoServicio;
use Servicios\CategoriaServicio;

class HomeControlador
{
    /** Pagina inicial: muestra los productos destacados */
    public function index(): void
    {
        $servProd = new ProductoServicio();
        $servCat  = new CategoriaServicio();

        $productos  = $servProd->obtenerCatalogo();
        $categorias = $servCat->listarTodas();

        // Mostramos como destacados los 4 primeros
        $destacados = array_slice($productos, 0, 4);

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/home/inicio.php';
        require APP . '/Vistas/comunes/pie.php';
    }
}
