<?php
namespace Requests;

class RegistroRequest
{
    // Valida los datos del formulario y devuelve un array con los errores encontrados
    public static function validar(array $datos): array
    {
        $errores = [];

        $nombre = trim($datos['nombre'] ?? '');
        $email  = trim($datos['email'] ?? '');
        $clave  = $datos['clave'] ?? '';
        $clave2 = $datos['clave2'] ?? '';

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

        return $errores;
    }
}
