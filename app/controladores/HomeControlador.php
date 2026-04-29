<?php
/**
 * Controlador de la pagina principal.
 */
class HomeControlador
{
    /** Pagina inicial: muestra los productos destacados */
    public function index(): void
    {
        $modeloProducto  = new Producto();
        $modeloCategoria = new Categoria();

        $productos  = $modeloProducto->listar();
        $categorias = $modeloCategoria->listar();

        // Mostramos como destacados los 4 primeros
        $destacados = array_slice($productos, 0, 4);

        require APP . '/vistas/comunes/cabecera.php';
        require APP . '/vistas/home/inicio.php';
        require APP . '/vistas/comunes/pie.php';
    }
}
