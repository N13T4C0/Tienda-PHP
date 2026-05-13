<?php

namespace Core;

/**
 * Clase base para los Modelos de datos del proyecto.
 *
 * Un Modelo representa una entidad del dominio (Producto, Usuario, Pedido...).
 * Esta clase base proporciona dos utilidades comunes:
 *   - rellenar(): hidrata el objeto desde un array (tipicamente una fila de BD)
 *   - aArray(): convierte el objeto de vuelta a un array asociativo
 *
 * Uso:
 *   class Producto extends BaseModelo {
 *       public int    $id       = 0;
 *       public string $nombre   = '';
 *       public float  $precio   = 0.0;
 *   }
 *
 *   $p = new Producto();
 *   $p->rellenar($filaDeBaseDeDatos);
 *   echo $p->nombre;
 */
abstract class BaseModelo
{
    /**
     * Rellena las propiedades del modelo con un array asociativo.
     * Solo asigna claves que existan como propiedad en la clase hija.
     *
     * @param  array $datos  Array con clave => valor (ej: resultado de PDO FETCH_ASSOC)
     * @return static        Devuelve $this para poder encadenar: (new Producto)->rellenar($fila)
     */
    public function rellenar(array $datos): static
    {
        foreach ($datos as $clave => $valor) {
            if (property_exists($this, $clave)) {
                $this->$clave = $valor;
            }
        }
        return $this;
    }

    /**
     * Convierte el modelo en un array asociativo con sus propiedades.
     * Util para pasar datos a las vistas o serializar a JSON.
     *
     * @return array<string, mixed>
     */
    public function aArray(): array
    {
        return get_object_vars($this);
    }
}
