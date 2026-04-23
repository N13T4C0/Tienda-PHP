<?php
namespace App\Requests;

class CategoriaRequest {
    private $errores = [];

    public function validar($datos) {
        if (empty($datos['nombre'])) {
            $this->errores['nombre'] = 'El nombre es obligatorio';
        } elseif (strlen(trim($datos['nombre'])) < 3) {
            $this->errores['nombre'] = 'El nombre debe tener al menos 3 caracteres';
        } elseif (strlen(trim($datos['nombre'])) > 100) {
            $this->errores['nombre'] = 'El nombre no puede superar los 100 caracteres';
        }

        if (isset($datos['descripcion']) && strlen($datos['descripcion']) > 500) {
            $this->errores['descripcion'] = 'La descripción no puede superar los 500 caracteres';
        }

        return empty($this->errores);
    }

    public function getErrores() {
        return $this->errores;
    }
}