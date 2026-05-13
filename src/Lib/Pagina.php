<?php
namespace Lib;

class Pagina{
    public static function renderizar(string $vista, array $datos = []): void
    {
        extract($datos);
        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/' . $vista . '.php';
        require APP . '/Vistas/comunes/pie.php';
    }
}