<?php
namespace App\Requests;

class RegistroRequest {
    private $errores = [];

    public function validar($datos) {
        // Nombre
        if (empty($datos['nombre']) || strlen(trim($datos['nombre'])) < 2) {
            $this->errores['nombre'] = 'El nombre es obligatorio (mínimo 2 caracteres)';
        }

        // Apellidos
        if (empty($datos['apellidos']) || strlen(trim($datos['apellidos'])) < 2) {
            $this->errores['apellidos'] = 'Los apellidos son obligatorios (mínimo 2 caracteres)';
        }

        // Email
        if (empty($datos['email'])) {
            $this->errores['email'] = 'El email es obligatorio';
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errores['email'] = 'El formato del email no es válido';
        }

        // Password
        if (empty($datos['password'])) {
            $this->errores['password'] = 'La contraseña es obligatoria';
        } elseif (strlen($datos['password']) < 6) {
            $this->errores['password'] = 'La contraseña debe tener al menos 6 caracteres';
        }

        return empty($this->errores);
    }

    public function getErrores() {
        return $this->errores;
    }
}