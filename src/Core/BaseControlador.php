<?php

namespace Core;

use Lib\Sesion;

/**
 * Clase base para todos los Controladores.
 *
 * Ofrece helpers comunes para no repetir el mismo codigo de
 * carga de vistas y redireccion en cada controlador.
 *
 * Uso:
 *   class AdminControlador extends BaseControlador { ... }
 */
abstract class BaseControlador
{
    /**
     * Carga una vista envuelta en la cabecera y pie comunes.
     *
     * Ejemplo:
     *   $this->renderizar('admin/panel');
     *   $this->renderizar('admin/productos', ['productos' => $lista]);
     *
     * @param string $vista  Ruta relativa dentro de src/Vistas/ (sin .php)
     * @param array  $datos  Variables que se inyectan en la vista via extract()
     */
    protected function renderizar(string $vista, array $datos = []): void
    {
        // extract() convierte cada clave del array en una variable local
        // Ej: ['productos' => [...]] → $productos disponible en la vista
        if (!empty($datos)) {
            extract($datos, EXTR_SKIP);
        }

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/' . $vista . '.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    /**
     * Redirige a una ruta relativa al proyecto.
     * Ejemplo: $this->redirigir('admin/productos');
     */
    protected function redirigir(string $ruta = ''): void
    {
        Sesion::redirigir($ruta);
    }

    /**
     * Guarda un mensaje flash y redirige en un solo paso.
     * Ejemplo: $this->flashYRedirigir('ok', 'Guardado', 'admin/productos');
     */
    protected function flashYRedirigir(string $tipo, string $texto, string $ruta): void
    {
        Sesion::mensaje($tipo, $texto);
        Sesion::redirigir($ruta);
    }
}
