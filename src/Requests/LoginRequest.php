<?php
namespace Requests;

class LoginRequest
{
    // Valida los datos del formulario y devuelve un array con los errores encontrados
    public static function validar(array $datos): array
    {
        $errores = [];

        //SANEAR
        $email = htmlspecialchars(trim($datos['email'] ?? ''), ENT_QUOTES, 'UTF-8');
        $clave = trim($datos['clave'] ?? '');

        // VALIDAR
        if ($email === '') {
            $errores[] = 'El email es obligatorio';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no tiene un formato valido';
        }

        if ($clave === '') {
            $errores[] = 'La clave es obligatoria';
        }

        return [
            'errores' => $errores,
            'datos' => [
                'email' => $email,
                'clave' => $clave
            ]
        ];
    }
}
