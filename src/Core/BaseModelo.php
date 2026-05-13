<?php

namespace Core;

/** Clase base para los Modelos — hidratacion desde array y conversion a array */
abstract class BaseModelo
{
    /** Rellena las propiedades del modelo con un array (ej: fila de BD) */
    public function rellenar(array $datos): static
    {
        foreach ($datos as $clave => $valor) {
            if (property_exists($this, $clave)) {
                $this->$clave = $valor;
            }
        }
        return $this;
    }

    /** Devuelve las propiedades del modelo como array asociativo */
    public function aArray(): array
    {
        return get_object_vars($this);
    }
}
