<?php

namespace Core;

use Lib\Sesion;

// cambio pedido maestra
/** Clase base para todos los Controladores — helpers de vistas y redireccion */
abstract class BaseControlador
{
    /** Carga una vista con cabecera y pie. Acepta variables opcionales para la vista */
    protected function view(string $vista, array $datos = []): void
    {
        if (!empty($datos)) {
            extract($datos, EXTR_SKIP);
        }
        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/' . $vista . '.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    /** Redirige a una ruta relativa al proyecto */
    protected function redirigir(string $ruta = ''): void
    {
        Sesion::redirigir($ruta);
    }

    /** Guarda un mensaje flash y redirige */
    protected function flashYRedirigir(string $tipo, string $texto, string $ruta): void
    {
        Sesion::mensaje($tipo, $texto);
        Sesion::redirigir($ruta);
    }
}
