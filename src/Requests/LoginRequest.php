<?php

namespace Requests;

class LoginRequest
{
    public static function validar(array $datos): array
    {
        $errores = [];

        $email = trim($datos['email'] ?? '');
        $clave = trim($datos['clave'] ?? '');

        if ($email === '') {
            $errores[] = 'El email es obligatorio';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no tiene un formato valido';
        } elseif (strlen($email) > 150) {
            $errores[] = 'El email no puede superar 150 caracteres';
        }

        $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

        if ($clave === '') {
            $errores[] = 'La contrasena es obligatoria';
        }

        return [
            'errores' => $errores,
            'datos'   => ['email' => $email, 'clave' => $clave],
        ];
    }
}
