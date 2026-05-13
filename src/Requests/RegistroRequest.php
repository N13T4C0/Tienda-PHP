<?php
namespace Requests;

class RegistroRequest
{
    // Valida los datos del formulario y devuelve un array con los errores encontrados
    public static function validar(array $datos): array
    {
        $errores = [];

        // SANITIZAR
        $nombre    = htmlspecialchars(trim($datos['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
        $apellidos = htmlspecialchars(trim($datos['apellidos'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email     = htmlspecialchars(trim($datos['email'] ?? ''), ENT_QUOTES, 'UTF-8');
        $clave     = trim($datos['clave'] ?? '');
        $clave2    = trim($datos['clave2'] ?? '');

        if ($nombre === '') {
            $errores[] = 'El nombre es obligatorio';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no tiene un formato valido';
        }

        if (strlen($clave) < 6) {
            $errores[] = 'La clave debe tener al menos 6 caracteres';
        }

        if ($clave !== $clave2) {
            $errores[] = 'Las claves no coinciden';
        }

        return [
            'errores' => $errores,
            'datos' => [
                'nombre'    => $nombre,
                'apellidos' => $apellidos,
                'email'     => $email,
                'clave'     => $clave
            ]
        ];
    }
}
