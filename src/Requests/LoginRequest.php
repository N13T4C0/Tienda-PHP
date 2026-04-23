<?php
namespace App\Requests;

class LoginRequest {
    private $errores = [];

    public function validar($datos) {
        // Email
        if (empty($datos['email'])) {
            $this->errores['email'] = 'El email es obligatorio';
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errores['email'] = 'El formato del email no es válido';
        }

        // Password
        if (empty($datos['password'])) {
            $this->errores['password'] = 'La contraseña es obligatoria';
        }

        return empty($this->errores);
    }

    public function getErrores() {
        return $this->errores;
    }
}